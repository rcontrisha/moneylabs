@extends('layouts.app')
@section('content')
<style>
    .checkout-cart-items,
    .checkout-totals {
        width: 100%;
        border-collapse: collapse;
    }

    .checkout-cart-items th,
    .checkout-cart-items td,
    .checkout-totals th,
    .checkout-totals td {
        padding: 8px 0;
        vertical-align: top;
    }

    .checkout-cart-items th:first-child,
    .checkout-cart-items td:first-child,
    .checkout-totals th:first-child,
    .checkout-totals td:first-child {
        text-align: left;
    }

    .checkout-cart-items th:last-child,
    .checkout-cart-items td:last-child,
    .checkout-totals th:last-child,
    .checkout-totals td:last-child {
        text-align: right;
    }

    .cart-total th,
    .cart-total td {
        color: green;
        font-weight: bold;
        font-size: 21px !important;
    }

    /* Custom btn-info dengan warna hijau (#19a74a) */
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

    /* Custom btn-success dengan warna utama #222, bentuk kotak */
    .btn-success {
        color: #fff;
        background-color: #222;
        border-color: #222;
        box-shadow: none;
        border-radius: 0;
        /* kotak */
    }

    .btn-success:hover {
        color: #fff;
        background-color: #111;
        /* lebih gelap dikit buat hover */
        border-color: #111;
        border-radius: 0;
        /* kotak */
    }

    .btn-success:focus,
    .btn-success:active,
    .btn-success.active,
    .show>.btn-success.dropdown-toggle {
        color: #fff;
        background-color: #000;
        /* lebih pekat pas diklik */
        border-color: #000;
        box-shadow: none;
        border-radius: 0;
        /* kotak */
    }

    .btn-success:disabled,
    .btn-success.disabled {
        color: #fff;
        background-color: #222;
        border-color: #222;
        border-radius: 0;
        /* kotak */
    }

    /* Custom btn-secondary dengan warna merah (#dc3545), bentuk kotak */
    .btn-secondary {
        color: #fff;
        background-color: #dc3545;
        /* merah */
        border-color: #dc3545;
        box-shadow: none;
        border-radius: 0;
        /* kotak */
    }

    .btn-secondary:hover {
        color: #fff;
        background-color: #b02a37;
        /* lebih gelap pas hover */
        border-color: #b02a37;
        border-radius: 0;
    }

    .btn-secondary:focus,
    .btn-secondary:active,
    .btn-secondary.active,
    .show>.btn-secondary.dropdown-toggle {
        color: #fff;
        background-color: #a52834;
        /* pekat pas klik */
        border-color: #a52834;
        box-shadow: none;
        border-radius: 0;
    }

    .btn-secondary:disabled,
    .btn-secondary.disabled {
        color: #fff;
        background-color: #e4606d;
        /* versi lebih pucat pas disabled */
        border-color: #e4606d;
        border-radius: 0;
    }

    /* --- ORDER TABLE STYLE --- */
    .checkout__totals h3 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .checkout-cart-items,
    .checkout-totals {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .checkout-cart-items th,
    .checkout-cart-items td,
    .checkout-totals th,
    .checkout-totals td {
        padding: 10px 8px;
        border-bottom: 1px solid #eee;
        font-size: 15px;
        vertical-align: middle;
    }

    .checkout-cart-items th {
        text-transform: uppercase;
        font-weight: 600;
        color: #444;
        font-size: 14px;
        letter-spacing: 0.5px;
    }

    .checkout-cart-items td {
        color: #666;
    }

    .checkout-cart-items td.text-right,
    .checkout-totals td.text-right {
        text-align: right;
        font-weight: 500;
        color: #111;
    }

    .checkout-totals th {
        text-transform: uppercase;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .checkout-totals td {
        font-weight: 500;
        color: #111;
    }

    .checkout-totals .cart-total th,
    .checkout-totals .cart-total td {
        font-size: 18px;
        font-weight: 700;
        color: #00757F;
        /* hijau branding kamu */
    }

</style>
<main style="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">Shipping and Checkout</h2>
        <div class="checkout-steps">
            <a href="{{route('cart.index')}}" class="checkout-steps__item active"
                style="color:#222;text-decoration:none;font-weight:700;">
                <span class="checkout-steps__item-number" style="color:#222;text-decoration:none;">01</span>
                <span class="checkout-steps__item-title" style="color:#222;text-decoration:none;">
                    <span style="color:#222;text-decoration:none;">Shopping Bag</span>
                    <em style="display:block;font-style:normal;color:#888;text-decoration:none;font-weight:400;">Manage
                        Your Items List</em>
                </span>
            </a>
            <a href="{{route('cart.checkout')}}" class="checkout-steps__item active"
                style="color:#222;text-decoration:none;">
                <span class="checkout-steps__item-number" style="color:#222;text-decoration:none;">02</span>
                <span class="checkout-steps__item-title" style="color:#222;text-decoration:none;">
                    <span style="color:#222;text-decoration:none;">Shipping and Checkout</span>
                    <em style="display:block;font-style:normal;color:#888;text-decoration:none;font-weight:400;">Checkout
                        Your Items List</em>
                </span>
            </a>
            <a href="{{route('cart.confirmation')}}" class="checkout-steps__item"
                style="color:#222;text-decoration:none;">
                <span class="checkout-steps__item-number" style="color:#222;text-decoration:none;">03</span>
                <span class="checkout-steps__item-title" style="color:#222;text-decoration:none;">
                    <span style="color:#222;text-decoration:none;">Confirmation</span>
                    <em style="display:block;font-style:normal;color:#888;text-decoration:none;font-weight:400;">Order
                        Confirmation</em>
                </span>
            </a>
        </div>
        <form name="checkout-form" action="{{route('cart.place.order')}}" method="POST">
            @csrf
            <div class="checkout-form">
                <div class="billing-info__wrapper">
                    <div class="row">
                        <div class="col-6">
                            <h4>SHIPPING DETAILS</h4>
                        </div>
                        <div class="col-6">
                            @if($address)
                            <a href="#" id="change-address-btn" class="btn btn-info btn-sm float-right">Change
                                Address</a>
                            @endif
                        </div>
                    </div>
                    @if($address)
                    {{-- View address --}}
                    <div id="address-view" class="row">
                        <div class="col-md-12">
                            <div class="my-account__address-list">
                                <div class="my-account__address-item">
                                    <div class="my-account__address-item__detail">
                                        <p>{{$address->name}}</p>
                                        <p>{{$address->address}}</p>
                                        <p>{{$address->landmark}}</p>
                                        <p>{{$address->city}}, {{$address->state}}, {{$address->country}}</p>
                                        <p>{{$address->zip}}</p>
                                        <p>Phone :- {{$address->phone}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form edit (hidden default) --}}
                    <div id="address-form" class="row mt-4" style="display:none;">
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="name" value="{{ $address->name }}">
                                <label for="name">Full Name *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="phone" value="{{ $address->phone }}">
                                <label for="phone">Phone Number *</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="zip" value="{{ $address->zip }}">
                                <label for="zip">Pincode *</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mt-3 mb-3">
                                <input type="text" class="form-control" name="state" value="{{ $address->state }}">
                                <label for="state">State *</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="city" value="{{ $address->city }}">
                                <label for="city">Town / City *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="address" value="{{ $address->address }}">
                                <label for="address">House no, Building Name *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="locality"
                                    value="{{ $address->locality }}">
                                <label for="locality">Road Name, Area, Colony *</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="landmark"
                                    value="{{ $address->landmark }}">
                                <label for="landmark">Landmark *</label>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <button type="button" id="save-address-btn" class="btn btn-success">Save Address</button>
                            <button type="button" id="cancel-edit-btn" class="btn btn-secondary">Cancel</button>
                        </div>
                    </div>
                    @else
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="name" value="{{old('name')}}">
                                <label for="name">Nama Lengkap *</label>
                                <span class="text-danger">@error('name') {{$message}} @enderror</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="phone" value="{{old('phone')}}">
                                <label for="phone">Nomor Telepon *</label>
                                <span class="text-danger">@error('phone') {{$message}} @enderror</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="zip" value="{{old('zip')}}">
                                <label for="zip">Kode Pos *</label>
                                <span class="text-danger">@error('zip') {{$message}} @enderror</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mt-3 mb-3">
                                <input type="text" class="form-control" name="state" value="{{old('state')}}">
                                <label for="state">Provinsi *</label>
                                <span class="text-danger">@error('state') {{$message}} @enderror</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="city" value="{{old('city')}}">
                                <label for="city">Kabupaten/Kota *</label>
                                <span class="text-danger">@error('city') {{$message}} @enderror</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="address" value="{{old('address')}}">
                                <label for="address">Nama Gedung, Nomor Rumah *</label>
                                <span class="text-danger">@error('address') {{$message}} @enderror</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="locality" value="{{old('locality')}}">
                                <label for="locality">Nama Jalan, Area, Kawasan/Kompleks *</label>
                                <span class="text-danger">@error('locality') {{$message}} @enderror</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="landmark" value="{{old('landmark')}}">
                                <label for="landmark">Detail Alamat (Patokan) *</label>
                                <span class="text-danger">@error('landmark') {{$message}} @enderror</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="checkout__totals-wrapper">
                    <div class="sticky-content">
                        <div class="checkout__totals">
                            <h3>Your Order</h3>
                            <table class="checkout-cart-items">
                                <thead>
                                    <tr>
                                        <th>PRODUCT</th>
                                        <th class="text-right">SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (Cart::instance('cart')->content() as $item)
                                    <tr>
                                        <td style="max-width:100px; text-align:justify;">
                                            {{ $item->name }} x {{ $item->qty }}
                                        </td>
                                        <td>
                                            IDR {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(Session::has('discounts'))
                            <table class="checkout-totals">
                                <tbody>
                                    <tr>
                                        <th>Subtotal</th>
                                        <td class="text-right">
                                            IDR
                                            {{ number_format((float) str_replace(',', '', Cart::instance('cart')->subtotal()), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Discount {{ Session("coupon")["code"] }}</th>
                                        <td class="text-right">
                                            -IDR
                                            {{ number_format((float) str_replace(',', '', Session("discounts")["discount"]), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Subtotal After Discount</th>
                                        <td class="text-right">
                                            IDR
                                            {{ number_format((float) str_replace(',', '', Session("discounts")["subtotal"]), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>SHIPPING</th>
                                        <td class="text-right">Free</td>
                                    </tr>
                                    <tr>
                                        <th>PPN (11%)</th>
                                        <td class="text-right">
                                            IDR
                                            {{ number_format((float) str_replace(',', '', Session("discounts")["tax"]), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr class="cart-total">
                                        <th>Total</th>
                                        <td class="text-right">
                                            IDR
                                            {{ number_format((float) str_replace(',', '', Session("discounts")["total"]), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            @else
                            <table class="checkout-totals">
                                <tbody>
                                    <tr>
                                        <th>SUBTOTAL</th>
                                        <td class="text-right">
                                            IDR
                                            {{ number_format((float) str_replace(',', '', Cart::instance('cart')->subtotal()), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>SHIPPING</th>
                                        <td class="text-right">Free</td>
                                    </tr>
                                    <tr>
                                        <th>PPN (11%)</th>
                                        <td class="text-right">
                                            IDR
                                            {{ number_format((float) str_replace(',', '', Cart::instance('cart')->tax()), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr class="cart-total">
                                        <th>TOTAL</th>
                                        <td class="text-right">
                                            IDR
                                            {{ number_format((float) str_replace(',', '', Cart::instance('cart')->total()), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            @endif
                        </div>
                        <!-- Metode Pembayaran -->
                        <div class="checkout__payment-methods">
                            <h4 class="mb-3">Payment Method</h4>

                            <div class="form-check mb-2">
                                <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                                    value="snap" id="mode_snap" checked>
                                <label class="form-check-label" for="mode_snap">
                                    Bayar Online (Midtrans Snap) <br>
                                    <small class="text-muted">Transfer Bank, e-Wallet, QRIS, dll</small>
                                </label>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                                    value="cod" id="mode_cod">
                                <label class="form-check-label" for="mode_cod">
                                    Cash on Delivery (COD)
                                </label>
                            </div>

                            <div class="policy-text">
                                Your personal data will be used to process your order, support your experience
                                throughout this website, and for other purposes described in our <a href="terms.html"
                                    target="_blank">privacy policy</a>.
                            </div>
                        </div>
                        <button id="place-order-btn" class="btn btn-primary">Place Order</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Modal Payment Pending -->
        <div class="modal fade" id="paymentPendingModal" tabindex="-1" aria-labelledby="paymentPendingLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentPendingLabel">Payment Pending</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Payment belum selesai. Silakan lanjutkan pembayaran atau kembali ke halaman checkout.
                </div>
                <div class="modal-footer">
                    <a href="/checkout" class="btn btn-primary">Kembali ke Checkout</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-NIIUrbvNmUVL5lsn"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const btnChange = document.getElementById("change-address-btn");
        const btnCancel = document.getElementById("cancel-edit-btn");
        const btnSave = document.getElementById("save-address-btn");
        const view = document.getElementById("address-view");
        const form = document.getElementById("address-form");

        // toggle edit mode
        btnChange?.addEventListener("click", e => {
            e.preventDefault();
            view.style.display = "none";
            form.style.display = "flex";
        });

        btnCancel?.addEventListener("click", e => {
            e.preventDefault();
            form.style.display = "none";
            view.style.display = "flex";
        });

        // save address (ajax)
        btnSave?.addEventListener("click", () => {
            const checkoutForm = document.forms['checkout-form'];
            const formData = new FormData(checkoutForm);

            fetch('{{ route("address.save") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    location.reload(); // atau bisa update DOM langsung biar smooth
                });
        });
    });

    document.getElementById('place-order-btn').addEventListener('click', function (e) {
        e.preventDefault();

        const form = document.forms['checkout-form'];
        const formData = new FormData(form);

        fetch('/place-order', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.snap_token && data.snap_token.original.token) {
                window.snap.pay(data.snap_token.original.token, {
                    onSuccess: function (result) {
                        window.location.href = `/order-confirmation?status=success&order_id=${result.order_id}`;
                    },
                    onClose: function () {
                        console.log('Payment popup closed');
                        // Tampilkan modal
                        var modal = new bootstrap.Modal(document.getElementById('paymentPendingModal'));
                        modal.show();
                        return false; 
                    }
                });
            } else {
                var modal = new bootstrap.Modal(document.getElementById('paymentPendingModal'));
                modal.show();
            }
        });
    });

</script>

@endsection
