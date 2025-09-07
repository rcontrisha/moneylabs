@extends('layouts.app')
@section('content')
<style>
    .cart-totals td {
        text-align: left;
    }

    .cart-totals th {
        text-align: left;
    }

    .cart-total th,
    .cart-total td {
        color: #00757F;
        font-weight: bold;
        font-size: 21px !important;
    }

    /* Custom btn-info dengan warna utama #00757F */
    .btn-info {
        color: #fff;
        background-color: #00757F;
        border-color: #00757F;
        box-shadow: none;
    }

    .btn-info:hover {
        color: #fff;
        background-color: #00666f;
        /* lebih gelap dikit buat hover */
        border-color: #00666f;
    }

    .btn-info:focus,
    .btn-info:active,
    .btn-info.active,
    .show>.btn-info.dropdown-toggle {
        color: #fff;
        background-color: #00595f;
        /* lebih pekat pas diklik */
        border-color: #00595f;
        box-shadow: none;
    }

    .btn-info:disabled,
    .btn-info.disabled {
        color: #fff;
        background-color: #00757F;
        border-color: #00757F;
    }
</style>
<main class="pt-90 px-32">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">Cart</h2>
        <div class="checkout-steps">
            <a href="{{route('cart.index')}}" class="checkout-steps__item active" style="color:#222;text-decoration:none;font-weight:700;">
                <span class="checkout-steps__item-number" style="color:#222;text-decoration:none;">01</span>
                <span class="checkout-steps__item-title" style="color:#222;text-decoration:none;">
                    <span style="color:#222;text-decoration:none;">Shopping Bag</span>
                    <em style="display:block;font-style:normal;color:#888;text-decoration:none;font-weight:400;">Manage Your Items List</em>
                </span>
            </a>
            <a href="{{route('cart.checkout')}}" class="checkout-steps__item" style="color:#222;text-decoration:none;">
                <span class="checkout-steps__item-number" style="color:#222;text-decoration:none;">02</span>
                <span class="checkout-steps__item-title" style="color:#222;text-decoration:none;">
                    <span style="color:#222;text-decoration:none;">Shipping and Checkout</span>
                    <em style="display:block;font-style:normal;color:#888;text-decoration:none;font-weight:400;">Checkout Your Items List</em>
                </span>
            </a>
            <a href="{{route('cart.confirmation')}}" class="checkout-steps__item" style="color:#222;text-decoration:none;">
                <span class="checkout-steps__item-number" style="color:#222;text-decoration:none;">03</span>
                <span class="checkout-steps__item-title" style="color:#222;text-decoration:none;">
                    <span style="color:#222;text-decoration:none;">Confirmation</span>
                    <em style="display:block;font-style:normal;color:#888;text-decoration:none;font-weight:400;">Order Confirmation</em>
                </span>
            </a>
        </div>
        <div class="shopping-cart">
            @if($cartItems->count() > 0)
            <div class="cart-table__wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th></th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems as $cartItem)
                        <tr>
                            <!-- Image -->
                            <td>
                                <div class="shopping-cart__product-item">
                                    <img loading="lazy" src="{{asset('uploads/products')}}/{{$cartItem->model->image}}"
                                        alt="" />
                                </div>
                            </td>

                            <!-- Product Info -->
                            <td>
                                <div class="shopping-cart__product-item__detail">
                                    <h4 class="product-title">{{$cartItem->name}}</h4>
                                    <ul class="shopping-cart__product-item__options">
                                        @if($cartItem->options->condition)
                                        <li><strong>Condition:</strong>
                                            {{ ucfirst(str_replace('_',' ',$cartItem->options->condition)) }}</li>
                                        @endif
                                        @if($cartItem->options->size)
                                        <li><strong>Size (US):</strong> {{ $cartItem->options->size }}</li>
                                        @endif
                                    </ul>
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="text-center">
                                <span class="shopping-cart__product-price">
                                    IDR {{ number_format($cartItem->price, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Quantity -->
                            <td class="text-center">
                                <div class="qty-box">
                                    <form method="POST"
                                        action="{{route('cart.reduce.qty',['rowId'=>$cartItem->rowId])}}"
                                        class="qty-btn-form">
                                        @csrf @method('PUT')
                                        <button type="submit" class="qty-btn">-</button>
                                    </form>

                                    <input type="number" name="quantity" value="{{$cartItem->qty}}" min="1" readonly
                                        class="qty-input">

                                    <form method="POST"
                                        action="{{route('cart.increase.qty',['rowId'=>$cartItem->rowId])}}"
                                        class="qty-btn-form">
                                        @csrf @method('PUT')
                                        <button type="submit" class="qty-btn">+</button>
                                    </form>
                                </div>
                            </td>

                            <!-- Subtotal -->
                            <td class="text-center">
                                <span class="shopping-cart__subtotal">
                                    IDR {{$cartItem->subTotal()}}
                                </span>
                            </td>

                            <!-- Remove -->
                            <td class="text-center">
                                <form method="POST" action="{{route('cart.remove',['rowId'=>$cartItem->rowId])}}">
                                    @csrf @method("DELETE")
                                    <button type="submit" class="remove-cart-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="cart-table-footer">
                    @if(!Session::has("coupon"))
                    <form class="position-relative bg-body coupon-form" method="POST"
                        action="{{route('cart.coupon.apply')}}">
                        @csrf
                        <input class="form-control" type="text" name="coupon_code" placeholder="Coupon Code">
                        <input class="btn-link fw-medium position-absolute top-0 end-0 h-100" type="submit"
                            value="APPLY COUPON">
                    </form>
                    @else
                    <form class="position-relative bg-body coupon-form" method="POST"
                        action="{{ route('cart.coupon.remove') }}">
                        @csrf
                        @method('DELETE')
                        <input class="form-control fw-bold" type="text" name="coupon_code" placeholder="Coupon Code"
                            value="{{ session()->get('coupon')['code'] }} Applied!" readonly
                            style="color: #198754; opacity: 1 !important; background-color: transparent;">

                        <button type="submit"
                            class="position-absolute top-0 end-0 h-100 px-4 fw-medium border-0 bg-transparent"
                            style="color: #dc3545; opacity: 1 !important;">
                            REMOVE COUPON
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{route('cart.empty')}}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-light clear-cart-btn" type="submit">CLEAR CART</button>
                    </form>
                </div>
            </div>
            <div class="shopping-cart__totals-wrapper">
                <div class="sticky-content">
                    <div class="shopping-cart__totals">
                        <h3>Cart Totals</h3>
                        @if(Session::has('discounts'))
                        <table class="cart-totals">
                            <tbody>
                                <tr>
                                    <th>Subtotal</th>
                                    <td>IDR {{Cart::instance('cart')->subtotal()}}</td>
                                </tr>
                                <tr>
                                    <th>Discount {{Session("coupon")["code"]}}</th>
                                    <td>-IDR {{Session("discounts")["discount"]}}</td>
                                </tr>
                                <tr>
                                    <th>Subtotal After Discount</th>
                                    <td>IDR {{Session("discounts")["subtotal"]}}</td>
                                </tr>
                                <tr>
                                    <th>SHIPPING</th>
                                    <td class="text-right">Free</td>
                                </tr>
                                <tr>
                                    <th>VAT</th>
                                    <td>IDR {{Session("discounts")["tax"]}}</td>
                                </tr>
                                <tr class="cart-total">
                                    <th>Total</th>
                                    <td>IDR {{Session("discounts")["total"]}}</td>
                                </tr>
                            </tbody>
                        </table>
                        @else
                        <table class="cart-totals">
                            <tbody>
                                <tr>
                                    <th>Subtotal</th>
                                    <td>IDR {{Cart::instance('cart')->subtotal()}}</td>
                                </tr>
                                <tr>
                                    <th>SHIPPING</th>
                                    <td class="text-right">Free</td>
                                </tr>
                                <tr>
                                    <th>VAT</th>
                                    <td>IDR {{Cart::instance('cart')->tax()}}</td>
                                </tr>
                                <tr class="cart-total">
                                    <th>Total</th>
                                    <td>IDR {{Cart::instance('cart')->total()}}</td>
                                </tr>
                            </tbody>
                        </table>
                        @endif
                    </div>
                    <div class="mobile_fixed-btn_wrapper">
                        <div class="button-wrapper container">
                            <a href="{{ route('cart.checkout') }}" class="btn btn-primary btn-checkout">PROCEED TO
                                CHECKOUT</a>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-md-12 text-center pt-5 pb-5">
                    <p>No item found in your cart</p>
                    <a href="{{route('shop.index')}}" class="btn btn-info">Shop Now</a>
                </div>
            </div>
            @endif
        </div>
    </section>
</main>
@endsection

@push("scripts")

<script>
    $(function () {
        $(".qty-control__increase").on("click", function () {
            $(this).closest('form').submit();
        })

        $(".qty-control__reduce").on("click", function () {
            $(this).closest('form').submit();
        })

        $('.remove-cart').on("click", function () {
            $(this).closest('form').submit();
        });
    });

</script>

@endpush
