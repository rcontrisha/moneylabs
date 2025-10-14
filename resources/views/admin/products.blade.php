@extends('layouts.admin')
@section('content')
<style>
    .table-striped th:nth-child(1),
    .table-striped td:nth-child(1) {
        width: 60px;
    }
    .pname img {
        width: 55px;
        height: 55px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 10px;
    }
    .pname .name {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .variant-summary {
        font-size: 13px;
        color: #555;
        line-height: 1.4;
    }
    .variant-summary strong {
        color: #111;
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Products</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{route('admin.index')}}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Products</div></li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap mb-3">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." name="name" tabindex="2">
                        </fieldset>
                        <div class="button-submit">
                            <button type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{ route('admin.product.add') }}">
                    <i class="icon-plus"></i> Add new
                </a>
            </div>

            <div class="table-responsive">
                @if(Session::has('status'))
                <p class="alert alert-success">{{Session::get('status')}}</p>
                @endif

                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Slug</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Stock</th>
                            <th>Variants</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                        @php
                            $variants = json_decode($product->variants, true);

                            // default values
                            $brandNewCount = $usedCount = 0;
                            $brandNewMin = $brandNewMax = null;
                            $usedMin = $usedMax = null;

                            if ($variants) {
                                // Brand New
                                if (isset($variants['brand_new'])) {
                                    $brandNewCount = count($variants['brand_new']);
                                    foreach ($variants['brand_new'] as $v) {
                                        $p = (int)$v['sale_price'];
                                        if ($p > 0) {
                                            $brandNewMin = is_null($brandNewMin) ? $p : min($brandNewMin, $p);
                                            $brandNewMax = is_null($brandNewMax) ? $p : max($brandNewMax, $p);
                                        }
                                    }
                                }
                                // Used
                                if (isset($variants['used'])) {
                                    $usedCount = count($variants['used']);
                                    foreach ($variants['used'] as $v) {
                                        $p = (int)$v['sale_price'];
                                        if ($p > 0) {
                                            $usedMin = is_null($usedMin) ? $p : min($usedMin, $p);
                                            $usedMax = is_null($usedMax) ? $p : max($usedMax, $p);
                                        }
                                    }
                                }
                            }
                        @endphp

                        <tr>
                            <td>{{$product->id}}</td>
                            <td class="pname d-flex align-items-center">
                                <img src="{{ asset('uploads/products/'.$product->image) }}" alt="">
                                <div class="name">
                                    <span class="fw-semibold">{{$product->name}}</span>
                                </div>
                            </td>
                            <td>{{$product->slug}}</td>
                            <td>{{$product->category->name ?? '-'}}</td>
                            <td>{{$product->brand->name ?? '-'}}</td>
                            <td>{{$product->stock_status}}</td>
                            <td>
                                @if($variants)
                                    <div class="variant-summary">
                                        @if($brandNewCount > 0)
                                            <strong>Brand New:</strong> {{ $brandNewCount }} size<br>
                                            Rp{{ number_format($brandNewMin,0,',','.') }} - Rp{{ number_format($brandNewMax,0,',','.') }}<br>
                                        @endif
                                        @if($usedCount > 0)
                                            <strong>Used:</strong> {{ $usedCount }} size<br>
                                            Rp{{ number_format($usedMin,0,',','.') }} - Rp{{ number_format($usedMax,0,',','.') }}
                                        @endif
                                        @if($brandNewCount == 0 && $usedCount == 0)
                                            <span class="text-muted">No variants</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">No variants</span>
                                @endif
                            </td>
                            <td>
                                <div class="list-icon-function d-flex gap-2">
                                    <a href="#" class="item eye"><i class="icon-eye"></i></a>
                                    <a href="{{ route('admin.product.edit',['id'=>$product->id]) }}" class="item edit">
                                        <i class="icon-edit-3"></i>
                                    </a>
                                    <form action="{{ route('admin.product.delete',['id'=>$product->id]) }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="item text-danger delete border-0 bg-transparent">
                                            <i class="icon-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{$products->links('pagination::bootstrap-5')}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    $(".delete").on('click',function(e){
        e.preventDefault();
        var selectedForm = $(this).closest('form');
        swal({
            title: "Are you sure?",
            text: "You want to delete this record?",
            type: "warning",
            buttons: ["No!", "Yes!"],
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (result) selectedForm.submit();  
        });                             
    });
});
</script>    
@endpush
