@extends('layouts.admin')
@section('content')
<style>
    .table-transaction>tbody>tr:nth-of-type(odd) {
        --bs-table-accent-bg: #fff !important;
    }

</style>
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Order Details</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Order Items</div>
                </li>
            </ul>
        </div>

        <div class="wg-box mt-5 mb-5">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Ordered Details</h5>
                </div>
                <a class="tf-button style-1 w208" href="{{route('admin.orders')}}">Back</a>
            </div>
            @if(Session::has('status'))
            <p class="alert alert-success">{{Session::get('status')}}</p>
            @endif
            <table class="table table-striped table-bordered table-transaction">
                <tr>
                    <th>Order No</th>
                    <td>{{"1" . str_pad($transaction->order->id,4,"0",STR_PAD_LEFT)}}</td>
                    <th>Mobile</th>
                    <td>{{$transaction->order->phone}}</td>
                    <th>Pin/Zip Code</th>
                    <td>{{$transaction->order->zip}}</td>
                </tr>
                <tr>
                    <th>Order Date</th>
                    <td>{{$transaction->order->created_at}}</td>
                    <th>Delivered Date</th>
                    <td>{{$transaction->order->delivered_date}}</td>
                    <th>Canceled Date</th>
                    <td>{{$transaction->order->canceled_date}}</td>
                </tr>
                <tr>
                    <th>Order Status</th>
                    <td colspan="5">
                        @php
                            $status = $transaction->order->status;
                            $badgeClass = match($status) {
                                'ordered' => 'bg-warning',
                                'approved' => 'bg-primary',
                                'shipped' => 'bg-info',
                                'delivered' => 'bg-success',
                                'canceled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            $statusLabel = ucfirst($status);
                        @endphp
                        <span class="badge {{ $badgeClass }} text-white fw-semibold px-3 py-2"
                            style="opacity:1 !important;">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="wg-box mt-5">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Ordered Items</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">SKU</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Brand</th>
                            <th class="text-center">Options</th>
                            <th class="text-center">Return Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderitems as $orderitem)
                        <tr>

                            <td class="pname">
                                <div class="image">
                                    <img src="{{asset('uploads/products/thumbnails')}}/{{$orderitem->product->image}}"
                                        alt="" class="image">
                                </div>
                                <div class="name">
                                    <a href="{{route('shop.product.details',["product_slug"=>$orderitem->product->slug])}}"
                                        target="_blank" class="body-title-2">{{$orderitem->product->name}}</a>
                                </div>
                            </td>
                            <td class="text-center">${{$orderitem->price}}</td>
                            <td class="text-center">{{$orderitem->quantity}}</td>
                            <td class="text-center">{{$orderitem->product->SKU}}</td>
                            <td class="text-center">{{$orderitem->product->category->name}}</td>
                            <td class="text-center">{{$orderitem->product->brand->name}}</td>
                            <td class="text-center">{{$orderitem->options}}</td>
                            <td class="text-center">{{$orderitem->rstatus == 0 ? "No":"Yes"}}</td>
                            <td class="text-center">
                                <a href="{{route('shop.product.details',["product_slug"=>$orderitem->product->slug])}}"
                                    target="_blank">
                                    <div class="list-icon-function view-icon">
                                        <div class="item eye">
                                            <i class="icon-eye"></i>
                                        </div>
                                    </div>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{$orderitems->links('pagination::bootstrap-5')}}
            </div>
        </div>
        <div class="wg-box mt-5">
            <h5>Shipping Address</h5>
            <div class="my-account__address-item col-md-6">
                <div class="my-account__address-item__detail">
                    <p>{{$transaction->order->name}}</p>
                    <p>{{$transaction->order->address}}</p>
                    <p>{{$transaction->order->locality}}</p>
                    <p>{{$transaction->order->city}}, {{$transaction->order->country}}</p>
                    <p>{{$transaction->order->landmark}}</p>
                    <p>{{$transaction->order->zip}}</p>
                    <br />
                    <p>Mobile : {{$transaction->order->phone}}</p>
                </div>
            </div>
        </div>
        <div class="wg-box mt-5">
            <h5>Transactions</h5>
            <table class="table table-striped table-bordered table-transaction">
                <tr>
                    <th>Subtotal</th>
                    <td>${{$transaction->order->subtotal}}</td>
                    <th>Tax</th>
                    <td>${{$transaction->order->tax}}</td>
                    <th>Discount</th>
                    <td>${{$transaction->order->discount}}</td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td>${{$transaction->order->total}}</td>
                    <th>Payment Mode</th>
                    <td>{{$transaction->mode}}</td>
                    <th>Status</th>
                    <td>
                        @switch($transaction->status)
                        @case('approved')
                        <span class="badge bg-success text-white fw-semibold px-3 py-2"
                            style="opacity:1 !important;">Approved</span>
                        @break

                        @case('declined')
                        <span class="badge bg-danger text-white fw-semibold px-3 py-2"
                            style="opacity:1 !important;">Declined</span>
                        @break

                        @case('refunded')
                        <span class="badge bg-secondary text-white fw-semibold px-3 py-2"
                            style="opacity:1 !important;">Refunded</span>
                        @break

                        @case('settlement')
                        <span class="badge bg-primary text-white fw-semibold px-3 py-2"
                            style="opacity:1 !important;">Settlement</span>
                        @break

                        @default
                        <span class="badge"
                            style="background-color:#ffc107; color:#000; font-weight:600; padding:0.5em 1em; opacity:1 !important;">
                            Pending
                        </span>
                        @endswitch
                    </td>
                </tr>
            </table>
        </div>
        <div class="wg-box mt-5">
            <h5>Update Order Status</h5>
            <form action="{{route('admin.order.status.update')}}" method="POST">
                @csrf
                @method("PUT")
                <input type="hidden" name="order_id" value="{{ $transaction->order->id }}" />
                <div class="row">
                    <div class="col-md-3">
                        <div class="select">
                            @php
                            $orderStatuses = [
                            'ordered' => 'Ordered',
                            'approved' => 'Approved',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                            'canceled' => 'Canceled'
                            ];

                            // Ambil status transaksi
                            $txnStatus = $transaction->status;
                            @endphp

                            <select id="order_status" name="order_status">
                                @foreach ($orderStatuses as $key => $label)
                                @php
                                $disabled = false;
                                // delivered hanya bisa dipilih jika transaction settlement/approved
                                if ($key == 'delivered' && !in_array($txnStatus, ['settlement','approved'])) {
                                $disabled = true;
                                }
                                @endphp
                                <option value="{{ $key }}" {{ $transaction->order->status == $key ? 'selected' : '' }}
                                    {{ $disabled ? 'disabled' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary tf-button w208">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
