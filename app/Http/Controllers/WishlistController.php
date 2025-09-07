<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Cart::instance('wishlist')->content();
        Log::info("Wishlist content: " . $wishlist);
        return view('wishlist', compact('wishlist'));
    }

    public function toggle($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $wishlist = \Surfsidemedia\Shoppingcart\Facades\Cart::instance('wishlist');

        $item = $wishlist->content()->where('id', $id)->first();

        if ($item) {
            // Hapus kalau udah ada di wishlist
            $wishlist->remove($item->rowId);
            return back()->with('message', 'Removed from wishlist');
        } else {
            // Ambil harga valid dari varian, sama kayak di ShopController
            $variants = json_decode($product->variants, true);
            $allVariants = [];

            if ($variants) {
                foreach ($variants as $condition => $items) {
                    foreach ($items as $v) {
                        $v['condition'] = $condition;
                        $allVariants[] = $v;
                    }
                }
            }

            $price = collect($allVariants)->min('sale_price');
            $price = $price ? (int)$price : 0;

            $wishlist->add(
                $product->id,
                $product->name,
                1,
                $price
            )->associate(\App\Models\Product::class);

            return back()->with('message', 'Added to wishlist');
        }
    }

    public function add_to_wishlist(Request $request)
    {
            Cart::instance('wishlist')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
            return redirect()->back();
    }

    public function remove_item_from_wishlist($rowId)
    {
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->back();
    }

    public function empty_wishlist()
    {
        Cart::instance('wishlist')->destroy();
        return redirect()->back();
    }

    public function move_to_cart($rowId)
    {
        $item = Cart::instance('wishlist')->get($rowId);
        Cart::instance('wishlist')->remove($rowId);
        Cart::instance('cart')->add($item->id,$item->name,1,$item->price)->associate('App\Models\Product');
        return redirect()->back();         
    }
}
