@extends('layouts.app')
@section('content')
<style>
    .order-box {
        max-width: 850px;
        margin: 0 auto 30px auto;
    }

    .order-details {
        border: 1px solid #222;
        background: #fff;
        font-size: 14px;
    }

    .order-info .flex-fill {
        padding: 0 10px;
        min-width: 180px;
    }

    .order-table th,
    .order-table td {
        padding: 10px 0;
        border-bottom: 1px dashed #ccc;
    }

    .order-table thead th {
        border-bottom: 2px solid #222;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    /* --- Highlight product rows --- */
    .order-table .item-row td {
        background: #fafafa;
        padding: 12px 0;
    }

    .order-table .item-row:nth-child(even) td {
        background: #f0f0f0;
    }

    /* --- Garis pemisah sebelum summary --- */
    .order-table .summary-row:first-of-type th,
    .order-table .summary-row:first-of-type td {
        border-top: 2px solid #222;
    }

    .order-table .summary-row th {
        font-weight: normal;
        text-transform: uppercase;
        font-size: 13px;
    }

    .order-table .total-row th,
    .order-table .total-row td {
        font-weight: bold;
        font-size: 15px;
        border-top: 2px solid #222;
    }

</style>

<main style="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">

        <!-- Success Icon + Message -->
        <div class="order-complete text-center mb-5">
            <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="40" cy="40" r="40" fill="#B9A16B" />
                <path
                    d="M52.9743 35.7612C52.9743 35.3426 52.8069 34.9241 52.5056 34.6228L50.2288 32.346C49.9275 32.0446 49.5089 31.8772 49.0904 31.8772C48.6719 31.8772 48.2533 32.0446 47.952 32.346L36.9699 43.3449L32.048 38.4062C31.7467 38.1049 31.3281 37.9375 30.9096 37.9375C30.4911 37.9375 30.0725 38.1049 29.7712 38.4062L27.4944 40.683C27.1931 40.9844 27.0257 41.4029 27.0257 41.8214C27.0257 42.24 27.1931 42.6585 27.4944 42.9598L33.5547 49.0201L35.8315 51.2969C36.1328 51.5982 36.5513 51.7656 36.9699 51.7656C37.3884 51.7656 37.8069 51.5982 38.1083 51.2969L40.385 49.0201L52.5056 36.8996C52.8069 36.5982 52.9743 36.1797 52.9743 35.7612Z"
                    fill="white" />
            </svg>
            <h2 class="mt-3">Your order is completed!</h2>
            <p>Thank you. Your order has been received.</p>
        </div>

        <!-- Order Info -->
        <div class="order-box p-4 mb-4" style="border: 2px dashed #d0d0d0;">
            <div class="d-flex justify-content-between text-center flex-wrap order-info">
                <div class="flex-fill">
                    <span class="d-block mb-1">Order Number</span>
                    <strong class="d-block text-truncate">{{ $order->order_code }}</strong>
                </div>
                <div class="flex-fill">
                    <span class="d-block mb-1">Date</span>
                    <strong class="d-block">{{ $order->created_at->format('d/m/Y') }}</strong>
                </div>
                <div class="flex-fill">
                    <span class="d-block mb-1">Total</span>
                    <strong class="d-block">IDR {{ number_format($order->total, 0, ',', '.') }}</strong>
                </div>
                <div class="flex-fill">
                    <span class="d-block mb-1">Payment Method</span>
                    <strong class="d-block">
                        {{ $transaction ? strtoupper($transaction->mode) : '-' }}
                    </strong>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="order-box p-4 order-details">
            <h5 class="mb-3">ORDER DETAILS</h5>
            <table class="table mb-0 order-table">
                <thead>
                    <tr>
                        <th>PRODUCT</th>
                        <th class="text-right">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderItems as $item)
                    <tr class="item-row">
                        <td>{{ $item->product->name }} x {{ $item->quantity }}</td>
                        <td class="text-right">IDR {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="summary-row">
                        <th>SUBTOTAL</th>
                        <td class="text-right">IDR {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="summary-row">
                        <th>SHIPPING</th>
                        <td class="text-right">Free shipping</td>
                    </tr>
                    <tr class="summary-row">
                        <th>VAT</th>
                        <td class="text-right">IDR {{ number_format($order->tax, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="total-row">
                        <th>TOTAL</th>
                        <td class="text-right">IDR {{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</main>
@endsection
