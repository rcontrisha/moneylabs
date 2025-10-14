@extends('layouts.app')
@section('content')
<style>
    .table> :not(caption)>tr>th {
        padding: 0.625rem 1.5rem .625rem !important;
        background-color: #6a6e51 !important;
    }

    .table>tr>td {
        padding: 0.625rem 1.5rem .625rem !important;
    }

    .table-bordered> :not(caption)>tr>th,
    .table-bordered> :not(caption)>tr>td {
        border-width: 1px 1px;
        border-color: #6a6e51;
    }

    .table> :not(caption)>tr>td {
        padding: .8rem 1rem !important;
    }

</style>
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Orders</h2>
        <div class="row">
            <div class="col-lg-2">
                @include('user.account-nav')
            </div>
            <div class="col-lg-10">
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 80px">OrderNo</th>
                                    <th>Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Subtotal</th>
                                    <th class="text-center">Tax</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-center">Delivered On</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <td class="text-center">{{"1" . str_pad($order->id,4,"0",STR_PAD_LEFT)}}</td>
                                    <td class="text-center">{{$order->name}}</td>
                                    <td class="text-center">{{$order->phone}}</td>
                                    <td class="text-center">IDR {{$order->subtotal}}</td>
                                    <td class="text-center">IDR {{$order->tax}}</td>
                                    <td class="text-center">IDR {{$order->total}}</td>
                                    <td class="text-center">
                                        @php
                                        $status = $order->status;
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
                                    <td class="text-center">{{$order->created_at}}</td>
                                    <td class="text-center">{{$order->orderItems->count()}}</td>
                                    <td>{{$order->delivered_date}}</td>
                                    <td class="text-center">
                                        <a href="{{route('user.acccount.order.details',['order_id'=>$order->id])}}">
                                            <div class="list-icon-function view-icon">
                                                <div class="item eye">
                                                    <i class="fa fa-eye"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                    <td>
                                        {{-- Tombol Download Invoice --}}
                                        @if ($order->invoice_path)
                                        <a href="{{ asset('storage/' . $order->invoice_path) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-download me-1"></i> Invoice
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{$orders->links('pagination::bootstrap-5')}}
                </div>
            </div>

        </div>
    </section>
</main>
@endsection
