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
        $size = $request->query('size') ? $request->query('size') : 12;
        $order = $request->query('order') ? $request->query('order') : -1;
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
        $min_price = $request->query('min') ? $request->query('min') : 1;
        $max_price = $request->query('max') ? $request->query('max') : 10000;

        if ($order == 1) {
            $products = Product::whereBetween('sale_price', [$min_price, $max_price])
                ->where(function ($query) use ($f_brands) {
                    $query->whereIn('brand_id', explode(',', $f_brands))
                        ->orWhereRaw("'" . $f_brands . "' = ''");
                })
                ->where(function ($query) use ($f_categories) {
                    $query->whereIn('category_id', explode(',', $f_categories))
                        ->orWhereRaw("'" . $f_categories . "' = ''");
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($size);
        } else if ($order == 2) {
            $products = Product::whereBetween('sale_price', [$min_price, $max_price])
                ->where(function ($query) use ($f_brands) {
                    $query->whereIn('brand_id', explode(',', $f_brands))
                        ->orWhereRaw("'" . $f_brands . "' = ''");
                })
                ->where(function ($query) use ($f_categories) {
                    $query->whereIn('category_id', explode(',', $f_categories))
                        ->orWhereRaw("'" . $f_categories . "' = ''");
                })
                ->orderBy('created_at', 'ASC')
                ->paginate($size);
        } else if ($order == 3) {
            $products = Product::whereBetween('sale_price', [$min_price, $max_price])
                ->where(function ($query) use ($f_brands) {
                    $query->whereIn('brand_id', explode(',', $f_brands))
                        ->orWhereRaw("'" . $f_brands . "' = ''");
                })
                ->where(function ($query) use ($f_categories) {
                    $query->whereIn('category_id', explode(',', $f_categories))
                        ->orWhereRaw("'" . $f_categories . "' = ''");
                })
                ->orderBy('sale_price', 'ASC')
                ->paginate($size);
        } else if ($order == 4) {
            $products = Product::whereBetween('sale_price', [$min_price, $max_price])
                ->where(function ($query) use ($f_brands) {
                    $query->whereIn('brand_id', explode(',', $f_brands))
                        ->orWhereRaw("'" . $f_brands . "' = ''");
                })
                ->where(function ($query) use ($f_categories) {
                    $query->whereIn('category_id', explode(',', $f_categories))
                        ->orWhereRaw("'" . $f_categories . "' = ''");
                })
                ->orderBy('sale_price', 'DESC')
                ->paginate($size);
        } else {
            $products = Product::whereBetween('sale_price', [$min_price, $max_price])
                ->where(function ($query) use ($f_brands) {
                    $query->whereIn('brand_id', explode(',', $f_brands))
                        ->orWhereRaw("'" . $f_brands . "' = ''");
                })
                ->where(function ($query) use ($f_categories) {
                    $query->whereIn('category_id', explode(',', $f_categories))
                        ->orWhereRaw("'" . $f_categories . "' = ''");
                })
                ->orderBy('id', 'DESC')
                ->paginate($size);
        }

        $brands = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();

        return view('shop', compact(
            'products',
            'size',
            'order',
            'brands',
            'categories',
            'f_brands',
            'f_categories',
            'min_price',
            'max_price'
        ));
    }

    public function product_details($product_slug)
    {
        $product = Product::where("slug",$product_slug)->first();
        $rproducts = Product::where("slug","<>",$product_slug)->get()->take(8);
        return view('details',compact("product","rproducts"));
    }
}
