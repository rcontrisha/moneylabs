<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            font-size: 14px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
        }
        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        table td {
            padding: 5px;
            vertical-align: top;
        }
        table tr td:nth-child(2) {
            text-align: right;
        }
        table tr.top table td {
            padding-bottom: 20px;
        }
        table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        table tr.information table td {
            padding-bottom: 40px;
        }
        table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        table tr.details td {
            padding-bottom: 20px;
        }
        table tr.item td {
            border-bottom: 1px solid #eee;
        }
        table tr.item.last td {
            border-bottom: none;
        }
        table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .align-right { text-align: right; }
        .status-paid { color: #28a745; font-weight: bold; font-size: 20px; }
    </style>
</head>
<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="4">
                <table>
                    <tr>
                        <td class="title">
                            <div class="logo">
                                <img src="{{ public_path('assets/images/logo_2.png') }}" 
                                     alt="Logo" 
                                     style="width: 120px; height: auto;">
                            </div>
                        </td>
                        <td class="align-right">
                            <h2>INVOICE</h2>
                            <b>Invoice #: ORD</b>{{ $order->order_code }}<br>
                            <b>Tanggal Dibuat:</b> {{ $order->created_at->format('d F Y') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="information">
            <td colspan="4">
                <table>
                    <tr>
                        <td>
                            <h3>Dari:</h3>
                            <strong>MoneyLabs Sneaker Store</strong><br>
                            Ngringin, Depok, Kab. Sleman<br>
                            Yogyakarta, 55281<br>
                            Indonesia
                        </td>
                        <td class="align-right">
                            <h3>Untuk:</h3>
                            <strong>{{ $order->name }}</strong><br>
                            {{ $order->address }}, {{ $order->locality }}<br>
                            {{ $order->city }}, {{ $order->state }} {{ $order->zip }}<br>
                            {{ $order->country }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <p class="status-paid">LUNAS</p>
            </td>
        </tr>
        <tr class="heading">
            <td>Produk</td>
            <td style="text-align: center;">Qty</td>
            <td style="text-align: right;">Harga Satuan</td>
            <td class="align-right">Total</td>
        </tr>
        @foreach($order->orderItems as $item)
        <tr class="item">
            <td>{{ $item->product->name }}</td>
            <td style="text-align: center;">{{ $item->quantity }}</td>
            <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="align-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="3" class="align-right">Subtotal</td>
            <td class="align-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr class="total">
            <td colspan="3" class="align-right">Diskon</td>
            <td class="align-right">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
        </tr>
        <tr class="total">
            <td colspan="3" class="align-right">Pajak (PPN 11%)</td>
            <td class="align-right">Rp {{ number_format($order->tax, 0, ',', '.') }}</td>
        </tr>
        <tr class="total" style="font-size: 18px;">
            <td colspan="3" class="align-right"><b>Total Akhir</b></td>
            <td class="align-right"><b>Rp {{ number_format($order->total, 0, ',', '.') }}</b></td>
        </tr>
    </table>
</div>
</body>
</html>