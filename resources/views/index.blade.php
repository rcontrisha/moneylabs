@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="hero-banner">
    <img src="{{ asset('assets/images/Frame 23.png') }}" alt="Special Price">
</div>

<section class="trending">
    <h2>Trending</h2>
    <div class="shoes-grid-2rows">
        @php
        // ambil produk terbaru/terpopuler langsung dari DB
        $trendingProducts = \App\Models\Product::latest()->take(14)->get();
        @endphp

        @foreach ($trendingProducts->chunk(7) as $row)
        <div class="shoes-row">
            @foreach ($row as $product)
            <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}" class="shoe-item-hover" style="text-decoration: none">
                <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}">
                <div class="shoe-name">
                    {{ $product->name }}
                </div>
            </a>
            @endforeach
        </div>
        @endforeach
    </div>

    <div class="brand-logos">
        <img src="{{ asset('assets/images/home/demo3/category_1.png') }}">
        <img src="{{ asset('assets/images/home/demo3/category_2.png') }}">
        <img src="{{ asset('assets/images/home/demo3/category_3.png') }}">
        <img src="{{ asset('assets/images/home/demo3/category_4.png') }}">
        <img src="{{ asset('assets/images/home/demo3/category_5.png') }}">
        <img src="{{ asset('assets/images/home/demo3/category_6.png') }}">
    </div>
</section>

<section class="category-section">
    <div class="category-container">
        <div class="category-box">
            <div class="category-text">RUNNING</div>
            <img src="assets/images/home/products/shoe1.png" alt="Running">
        </div>

        <div class="category-box">
            <div class="category-text">SCHOOL</div>
            <img src="assets/images/home/products/shoe12.png" alt="School">
        </div>

        <div class="category-box">
            <div class="category-text">SKATE</div>
            <img src="assets/images/home/products/shoe3.png" alt="Skate">
        </div>

        <div class="category-box">
            <div class="category-text">SNEAKERS</div>
            <img src="assets/images/home/products/shoe9.png" alt="Sneakers">
        </div>
    </div>
</section>

{{-- <section class="promo">
    <h2 class="green-title">ADHAin Promo 6.6</h2>
    <div class="promo-cards">
        @for ($i = 1; $i <= 3; $i++)
        <div class="promo-card">
        <div class="badge">20%</div>
        <div class="promo-text">
            <div class="promo-title">PUMA<br>SPEEDCAT</div>
            <div class="promo-subtitle">SEPATU RACING LOOK SKENA</div>
        </div>
        <img src="{{ asset("assets/images/home/products/shoe{$i}.png") }}" alt="Promo {{ $i }}">
</div>
@endfor
</div>
</section> --}}

<section class="banner-padel">
    <img src="{{ asset('assets/images/padel.png') }}" alt="Padel Line">
</section>
@endsection
