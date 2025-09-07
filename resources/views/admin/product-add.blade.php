@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add Product</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.products') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.products') }}"><div class="text-tiny">Products</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Add product</div></li>
            </ul>
        </div>

        <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data" action="{{ route('admin.product.store') }}">
            @csrf

            {{-- Basic Product Info --}}
            <div class="wg-box">
                <fieldset>
                    <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                    <input type="text" name="name" placeholder="Enter product name" value="{{ old('name') }}">
                </fieldset>
                @error('name') <span class="alert alert-danger">{{$message}}</span> @enderror

                <fieldset>
                    <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                    <input type="text" name="slug" placeholder="Enter slug" value="{{ old('slug') }}">
                </fieldset>
                @error('slug') <span class="alert alert-danger">{{$message}}</span> @enderror

                <div class="gap22 cols">
                    <fieldset>
                        <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
                        <select name="category_id">
                            <option value="">Choose category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                    @error('category_id') <span class="alert alert-danger">{{$message}}</span> @enderror

                    <fieldset>
                        <div class="body-title mb-10">Brand <span class="tf-color-1">*</span></div>
                        <select name="brand_id">
                            <option value="">Choose brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                    @error('brand_id') <span class="alert alert-danger">{{$message}}</span> @enderror
                </div>

                <fieldset>
                    <div class="body-title mb-10">Short Description <span class="tf-color-1">*</span></div>
                    <textarea name="short_description" placeholder="Short Description">{{ old('short_description') }}</textarea>
                </fieldset>
                @error('short_description') <span class="alert alert-danger">{{$message}}</span> @enderror

                <fieldset>
                    <div class="body-title mb-10">Description <span class="tf-color-1">*</span></div>
                    <textarea name="description" placeholder="Description">{{ old('description') }}</textarea>
                </fieldset>
                @error('description') <span class="alert alert-danger">{{$message}}</span> @enderror
            </div>

            {{-- Images --}}
            <div class="wg-box">
                <fieldset>
                    <div class="body-title">Main Image <span class="tf-color-1">*</span></div>
                    <input type="file" name="image" accept="image/*">
                </fieldset>
                @error('image') <span class="alert alert-danger">{{$message}}</span> @enderror

                <fieldset>
                    <div class="body-title">Gallery Images</div>
                    <input type="file" name="images[]" accept="image/*" multiple>
                </fieldset>
                @error('images') <span class="alert alert-danger">{{$message}}</span> @enderror
            </div>

            {{-- Variants --}}
            <div class="wg-box">
                <div class="body-title mb-10">Product Variants</div>

                {{-- Brand New --}}
                <div class="variant-block" data-type="brand_new">
                    <h5>Brand New</h5>
                    <div class="variant-rows gap10 mb-2">
                        <div class="gap10 cols variant-row">
                            <input type="text" class="form-control" placeholder="Size">
                            <input type="number" class="form-control" placeholder="Regular Price">
                            <input type="number" class="form-control" placeholder="Sale Price">
                            <input type="number" class="form-control" placeholder="Quantity">
                            <button type="button" class="btn-remove">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="btn-add" data-type="brand_new">+ Add Size</button>
                </div>

                {{-- Used --}}
                <div class="mt-3">
                    <label>
                        <input type="checkbox" id="hasUsedVariant"> This product also has a "Used" variant
                    </label>
                </div>

                <div class="variant-block mt-2" data-type="used" id="usedVariantBlock" style="display:none;">
                    <h5>Used</h5>
                    <div class="variant-rows gap10 mb-2">
                        <div class="gap10 cols variant-row">
                            <input type="text" class="form-control" placeholder="Size">
                            <input type="number" class="form-control" placeholder="Regular Price">
                            <input type="number" class="form-control" placeholder="Sale Price">
                            <input type="number" class="form-control" placeholder="Quantity">
                            <button type="button" class="btn-remove">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="btn-add" data-type="used">+ Add More</button>
                </div>

                <input type="hidden" name="variants" id="variantsInput">
            </div>

            {{-- Other Info --}}
            <div class="wg-box">
                <fieldset>
                    <div class="body-title mb-10">SKU <span class="tf-color-1">*</span></div>
                    <input type="text" name="SKU" placeholder="Enter SKU" value="{{ old('SKU') }}">
                </fieldset>
                @error('SKU') <span class="alert alert-danger">{{$message}}</span> @enderror

                <fieldset>
                    <div class="body-title mb-10">Stock Status</div>
                    <select name="stock_status">
                        <option value="instock">In Stock</option>
                        <option value="outofstock">Out of Stock</option>
                    </select>
                </fieldset>

                <fieldset>
                    <div class="body-title mb-10">Featured</div>
                    <select name="featured">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </fieldset>

                <button type="submit" class="tf-button w-full mt-3">Add Product</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function(){
        // Toggle Used variant
        $('#hasUsedVariant').change(function(){
            $('#usedVariantBlock').toggle(this.checked);
        });

        // Add new variant row
        $('.btn-add').click(function(){
            let type = $(this).data('type');
            let $block = $('.variant-block[data-type="'+type+'"] .variant-rows');
            let newRow = `
                <div class="gap10 cols variant-row">
                    <input type="text" class="form-control" placeholder="Size">
                    <input type="number" class="form-control" placeholder="Regular Price">
                    <input type="number" class="form-control" placeholder="Sale Price">
                    <input type="number" class="form-control" placeholder="Quantity">
                    <button type="button" class="btn-remove">Remove</button>
                </div>
            `;
            $block.append(newRow);
        });

        // Remove variant row
        $(document).on('click', '.btn-remove', function(){
            $(this).closest('.variant-row').remove();
        });

        // On submit, pack all variants into JSON
        $('form').on('submit', function(){
            let variants = {};

            $('.variant-block').each(function(){
                let type = $(this).data('type');
                let rows = [];
                $(this).find('.variant-row').each(function(){
                    let size = $(this).find('input').eq(0).val();
                    let regular_price = $(this).find('input').eq(1).val();
                    let sale_price = $(this).find('input').eq(2).val();
                    let quantity = $(this).find('input').eq(3).val();

                    if(size || regular_price || sale_price || quantity) { // skip empty rows
                        rows.push({size, regular_price, sale_price, quantity});
                    }
                });
                if(rows.length) variants[type] = rows;
            });

            $('#variantsInput').val(JSON.stringify(variants));
        });
    });

</script>
@endpush
