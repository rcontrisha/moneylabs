<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 10;
        $order = $request->query('order') ? $request->query('order') : -1;
        $f_brands = is_array($request->query('brands')) ? implode(',', $request->query('brands')) : ($request->query('brands') ?? '');
        $f_categories = is_array($request->query('categories')) ? implode(',', $request->query('categories')) : ($request->query('categories') ?? '');
        $min_price = $request->query('min') ? $request->query('min') : 1;
        $max_price = $request->query('max') ? $request->query('max') : 10000000;
        $keyword = $request->query('q'); // ✅ ambil keyword pencarian

        // --- FIX parsing conditions jadi array
        $f_conditions = $request->query('conditions');
        if ($f_conditions) {
            $f_conditions = is_array($f_conditions) ? $f_conditions : explode(',', $f_conditions);
        } else {
            $f_conditions = [];
        }

        // --- FIX parsing sizes jadi array
        $f_sizes = $request->query('sizes');
        if ($f_sizes) {
            $f_sizes = is_array($f_sizes) ? $f_sizes : explode(',', $f_sizes);
        } else {
            $f_sizes = [];
        }

        // Get all products
        $query = Product::query();

        // ✅ filter pencarian keyword
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Apply filters: brand & category
        $query->where(function ($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands))
                ->orWhereRaw("'" . $f_brands . "' = ''");
        })
        ->where(function ($query) use ($f_categories) {
            $query->whereIn('category_id', explode(',', $f_categories))
                ->orWhereRaw("'" . $f_categories . "' = ''");
        });

        // Get products and calculate variant prices
        $products = $query->get()->map(function ($product) {
            $variants = json_decode($product->variants, true);
            $minVariantPrice = PHP_INT_MAX;
            $maxVariantPrice = 0;

            if ($variants && is_array($variants)) {
                foreach ($variants as $condition) {
                    foreach ($condition as $variant) {
                        $price = (float) $variant['sale_price'];
                        if ($price < $minVariantPrice) $minVariantPrice = $price;
                        if ($price > $maxVariantPrice) $maxVariantPrice = $price;
                    }
                }
            } else {
                $minVariantPrice = $product->sale_price ? (float)$product->sale_price : 0;
                $maxVariantPrice = $minVariantPrice;
            }

            $product->min_variant_price = $minVariantPrice;
            $product->max_variant_price = $maxVariantPrice;
            return $product;
        });

        // Apply price filter
        $products = $products->filter(function ($product) use ($min_price, $max_price) {
            return $product->min_variant_price >= $min_price && 
                $product->min_variant_price <= $max_price;
        });

        // Apply condition + size filter (must match in same variant)
        $products = $products->filter(function ($product) use ($f_conditions, $f_sizes) {
            $variants = json_decode($product->variants, true);
            if (!$variants) return false;

            // kalau dua-duanya kosong → lolos semua
            if (empty($f_conditions) && empty($f_sizes)) {
                return true;
            }

            foreach ($variants as $condKey => $condVariants) {
                foreach ($condVariants as $variant) {
                    $sizeMatch = empty($f_sizes) || in_array($variant['size'], $f_sizes);
                    $condMatch = empty($f_conditions) || in_array($condKey, $f_conditions);

                    if ($sizeMatch && $condMatch) {
                        return true; // ✅ harus match barengan
                    }
                }
            }

            return false;
        });

        // Apply sorting
        if ($order == 1) {
            $products = $products->sortByDesc('created_at');
        } else if ($order == 2) {
            $products = $products->sortBy('created_at');
        } else if ($order == 3) {
            $products = $products->sortBy('min_variant_price');
        } else if ($order == 4) {
            $products = $products->sortByDesc('min_variant_price');
        } else {
            $products = $products->sortByDesc('id');
        }

        // Manual pagination
        $page = request('page', 1);
        $offset = ($page - 1) * $size;
        $paginatedItems = $products->slice($offset, $size);
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $products->count(),
            $size,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Get brands & categories
        $brands = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();

        // Extract unique conditions
        $allConditions = [];
        foreach (Product::pluck('variants') as $variantJson) {
            $variants = json_decode($variantJson, true);
            if ($variants && is_array($variants)) {
                $allConditions = array_merge($allConditions, array_keys($variants));
            }
        }
        $conditions = array_unique($allConditions);

        // Extract unique sizes
        $allSizes = [];
        foreach (Product::pluck('variants') as $variantJson) {
            $variants = json_decode($variantJson, true);
            if ($variants && is_array($variants)) {
                foreach ($variants as $condition) {
                    foreach ($condition as $variant) {
                        $allSizes[] = $variant['size'];
                    }
                }
            }
        }
        $sizes = array_unique($allSizes);

        return view('shop', compact(
            'products',
            'size',
            'order',
            'brands',
            'categories',
            'f_brands',
            'f_categories',
            'min_price',
            'max_price',
            'conditions',
            'sizes',
            'f_conditions',
            'f_sizes'
        ));
    }

    public function product_details($product_slug)
    {
        $product = Product::where("slug", $product_slug)->firstOrFail();
        $variants = json_decode($product->variants, true);
        $allVariants = [];

        foreach ($variants as $condition => $items) {
            foreach ($items as $item) {
                $item['condition'] = $condition;
                $allVariants[] = $item;
            }
        }

        // Cari harga terendah dari semua varian
        $minPrice = collect($allVariants)->min('sale_price');

        // Related: kategori sama atau brand sama, exclude produk ini
        $relatedQuery = Product::where('slug', '<>', $product_slug)
            ->where(function($q) use ($product) {
                $q->where('category_id', $product->category_id)
                ->orWhere('brand_id', $product->brand_id);
            });

        $relatedAll = $relatedQuery->get()->map(function($item) {
            $variants = json_decode($item->variants, true);
            $min = null;
            if ($variants) {
                foreach ($variants as $condition) {
                    foreach ($condition as $variant) {
                        $price = (int)$variant['sale_price'];
                        if (is_null($min) || $price < $min) {
                            $min = $price;
                        }
                    }
                }
            }
            $item->min_variant_price = $min ?? $item->sale_price;
            return $item;
        });

        $related = $relatedAll->take(4);

        return view('details', compact(
            "product",
            "related",
            "allVariants",
            "minPrice",
            "relatedAll"
        ));
    }
}
