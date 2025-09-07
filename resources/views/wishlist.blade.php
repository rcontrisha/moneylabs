@extends('layouts.app')

@section('title', 'Wishlist')

@section('content')

<style>
    .wishlist-actions {
        display: flex;
        gap: 10px;
    }

    .wishlist-actions .btn-apply {
        font-size: 14px;
        padding: 8px 12px;
        border-radius: 6px;
        font-weight: 500;
    }

</style>

<section class="catalog-page">
    <div class="catalog-container">

        <aside class="catalog-sidebar">
            <div class="filter-section">
                <h4>YOUR WISHLIST</h4>
                <p style="font-size:13px;color:#888;">
                    Produk yang kamu favoritkan akan muncul di sini.<br>
                    Klik <span style="color:#19a74a;">"Add to Cart"</span> untuk langsung belanja!
                </p>
            </div>
        </aside>

        <div class="catalog-content">
            <h2>Wishlist</h2>
            <div id="wishlist-grid" class="catalog-grid">
                @forelse($wishlist as $product)
                <div class="catalog-item">
                    <a href="{{ route('shop.product.details', ['product_slug' => $product->model->slug]) }}">
                        <img loading="lazy" src="{{ asset('uploads/products/' . $product->model->image) }}" width="160"
                            height="130" alt="{{ $product->name }}" class="pc__img">
                    </a>
                    <div class="catalog-item-info">
                        <p class="item-name">{{ $product->name }}</p>
                        <div class="item-spacer"></div>
                        <p class="item-price">
                            IDR {{ number_format($product->price) }}
                        </p>
                        <div class="wishlist-actions d-flex gap-2 mt-3">
                            <form method="POST" action="{{ route('wishlist.remove', $product->rowId) }}"
                                class="flex-fill">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-apply w-100"
                                    style="background:#fff;color:#e74c3c;border:1px solid #e74c3c;">
                                    Remove
                                </button>
                            </form>
                            <a href="{{ route('shop.product.details', ['product_slug' => $product->model->slug]) }}"
                                class="btn-apply w-100 text-center"
                                style="background:#000;color:#fff;display:inline-block;text-decoration:none;">
                                View & Add to Cart
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div style="padding:32px 0;color:#888;font-size:1.1em;">
                    Wishlist kamu masih kosong.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
