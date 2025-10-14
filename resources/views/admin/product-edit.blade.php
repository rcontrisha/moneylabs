@extends('layouts.admin')

@push('styles')
{{-- CSS untuk layout 2 kolom dan preview gambar modern --}}
<style>
    /* CSS UNTUK MEMBUAT KONTEN FULL-WIDTH */
    .main-content-wrap {
        max-width: none; /* Menghapus batas lebar maksimal */
        padding-bottom: 100px; /* Ruang untuk floating button */
    }

    /* CSS UNTUK LAYOUT 2 KOLOM YANG SAMA TINGGI */
    .form-layout {
      display: flex;
      flex-wrap: wrap;
      gap: 24px;
      align-items: stretch; /* Membuat kolom kiri & kanan sama tinggi */
    }
    .form-main {
      flex: 2;
      min-width: 350px;
      display: flex;
      flex-direction: column;
      gap: 24px;
    }
    .form-sidebar {
      flex: 1;
      min-width: 300px;
      display: flex;
      flex-direction: column;
    }
    .form-sidebar .wg-box {
        flex-grow: 1; /* Membuat card di sidebar mengisi ruang kosong */
    }
    @media (max-width: 991px) {
      .form-layout {
        flex-direction: column;
        align-items: normal;
      }
    }

    /* CSS untuk Image Uploader */
    .image-uploader { display: flex; flex-direction: column; gap: 20px; }
    .image-upload-box { width: 100%; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; overflow: hidden; transition: all 0.3s ease; }
    .image-upload-box:hover { border-color: #007bff; background: #f1f6ff; }
    .main-image-upload .image-upload-box { height: 250px; }
    .image-upload-box img.preview { width: 100%; height: 100%; object-fit: contain; position: absolute; top: 0; left: 0; }
    .upload-placeholder { text-align: center; color: #6c757d; }
    .upload-placeholder .icon { font-size: 32px; margin-bottom: 8px; }
    .gallery-previews { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin-top: 10px; }
    .gallery-item { position: relative; width: 100%; padding-top: 50%; padding-bottom: 50%; border-radius: 6px; overflow: hidden; border: 1px solid #dee2e6; }
    .gallery-item img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; }
    .remove-image { position: absolute; top: 4px; right: 4px; width: 20px; height: 20px; background-color: rgba(0, 0, 0, 0.6); color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; line-height: 1; cursor: pointer; opacity: 0; transition: opacity 0.2s; }
    .gallery-item:hover .remove-image { opacity: 1; }
    input[type="file"].d-none { display: none; }

    /* CSS untuk Floating Submit Button */
    .floating-submit-container { position: fixed; bottom: 30px; right: 40px; z-index: 999; }
    .floating-submit-container .tf-button { box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: transform 0.2s ease-out; }
    .floating-submit-container .tf-button:hover { transform: translateY(-3px); }

    /* CSS BARU UNTUK HEADER VARIAN & TOMBOL REMOVE */
    .variant-header-labels {
        padding-right: 52px; /* Memberi ruang kosong seukuran tombol remove */
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
        margin-bottom: 10px;
    }
    .variant-header-labels > div {
        font-size: 13px;
        font-weight: 600;
        color: #5e6278;
    }
    .btn-remove-variant {
        background-color: #f1416c;
        color: white;
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 6px;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-remove-variant:hover {
        background-color: #d9214e;
    }
</style>
@endpush

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Product</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.products') }}"><div class="text-tiny">Products</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Edit Product</div></li>
            </ul>
        </div>

        <form class="tf-section-1 form-add-product" method="POST" enctype="multipart/form-data" action="{{ route('admin.product.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $product->id }}">

            <div class="form-layout">
                {{-- Kolom Kiri --}}
                <div class="form-main">
                    {{-- Card: Product Information --}}
                    <div class="wg-box">
                        <fieldset>
                            <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                            <input type="text" name="name" placeholder="Enter product name" value="{{ old('name', $product->name) }}">
                        </fieldset>
                        <fieldset>
                            <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                            <input type="text" name="slug" placeholder="Enter slug" value="{{ old('slug', $product->slug) }}">
                        </fieldset>
                        <fieldset>
                            <div class="body-title mb-10">Short Description <span class="tf-color-1">*</span></div>
                            <textarea name="short_description" placeholder="Short Description">{{ old('short_description', $product->short_description) }}</textarea>
                        </fieldset>
                        <fieldset>
                            <div class="body-title mb-10">Description <span class="tf-color-1">*</span></div>
                            <textarea name="description" placeholder="Description">{{ old('description', $product->description) }}</textarea>
                        </fieldset>
                    </div>

                    {{-- Card: Variants --}}
                    <div class="wg-box">
                        <div class="body-title mb-10">Product Variants</div>
                        @php
                            $variants = json_decode($product->variants, true) ?? [];
                            $brandnew = $variants['brand_new'] ?? $variants['Brand New'] ?? [];
                            $used = $variants['used'] ?? $variants['Used'] ?? [];
                        @endphp

                        {{-- Brand New --}}
                        <div class="variant-block" data-type="brand_new">
                            <h5 class="mb-3">Brand New</h5>
                            {{-- BARIS HEADER LABEL --}}
                            <div class="gap10 cols variant-header-labels d-none d-md-flex">
                                <div>Size</div>
                                <div>Regular Price</div>
                                <div>Sale Price</div>
                                <div>Quantity</div>
                            </div>
                            <div class="variant-rows gap10 mb-2">
                                @forelse($brandnew as $bn)
                                <div class="gap10 cols variant-row">
                                    <input type="text" class="form-control" placeholder="Size" value="{{ $bn['size'] ?? '' }}">
                                    <input type="number" class="form-control" placeholder="Regular Price" value="{{ $bn['regular_price'] ?? '' }}">
                                    <input type="number" class="form-control" placeholder="Sale Price" value="{{ $bn['sale_price'] ?? '' }}">
                                    <input type="number" class="form-control" placeholder="Quantity" value="{{ $bn['quantity'] ?? '' }}">
                                    <button type="button" class="btn-remove-variant">&times;</button>
                                </div>
                                @empty
                                <div class="gap10 cols variant-row">
                                    <input type="text" class="form-control" placeholder="Size">
                                    <input type="number" class="form-control" placeholder="Regular Price">
                                    <input type="number" class="form-control" placeholder="Sale Price">
                                    <input type="number" class="form-control" placeholder="Quantity">
                                    <button type="button" class="btn-remove-variant">&times;</button>
                                </div>
                                @endforelse
                            </div>
                            <button type="button" class="btn-add" data-type="brand_new">+ Add Size</button>
                        </div>

                        <hr class="my-4">

                        {{-- Used --}}
                        <div class="mt-3">
                            <label>
                                <input type="checkbox" id="hasUsedVariant" {{ !empty($used) ? 'checked' : '' }}> This product also has a "Used" variant
                            </label>
                        </div>
                        <div class="variant-block mt-2" data-type="used" id="usedVariantBlock" style="{{ !empty($used) ? '' : 'display:none;' }}">
                            <h5 class="mb-3">Used</h5>
                            {{-- BARIS HEADER LABEL --}}
                            <div class="gap10 cols variant-header-labels d-none d-md-flex">
                                <div>Size</div>
                                <div>Regular Price</div>
                                <div>Sale Price</div>
                                <div>Quantity</div>
                            </div>
                            <div class="variant-rows gap10 mb-2">
                                @forelse($used as $us)
                                <div class="gap10 cols variant-row">
                                    <input type="text" class="form-control" placeholder="Size" value="{{ $us['size'] ?? '' }}">
                                    <input type="number" class="form-control" placeholder="Regular Price" value="{{ $us['regular_price'] ?? '' }}">
                                    <input type="number" class="form-control" placeholder="Sale Price" value="{{ $us['sale_price'] ?? '' }}">
                                    <input type="number" class="form-control" placeholder="Quantity" value="{{ $us['quantity'] ?? '' }}">
                                    <button type="button" class="btn-remove-variant">&times;</button>
                                </div>
                                @empty
                                <div class="gap10 cols variant-row">
                                    <input type="text" class="form-control" placeholder="Size">
                                    <input type="number" class="form-control" placeholder="Regular Price">
                                    <input type="number" class="form-control" placeholder="Sale Price">
                                    <input type="number" class="form-control" placeholder="Quantity">
                                    <button type="button" class="btn-remove-variant">&times;</button>
                                </div>
                                @endforelse
                            </div>
                            <button type="button" class="btn-add" data-type="used">+ Add More</button>
                        </div>
                        <input type="hidden" name="variants" id="variantsInput">
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="form-sidebar">
                    {{-- Card: Organization, Media & Status --}}
                    <div class="wg-box">
                        <div class="image-uploader">
                            {{-- Main Image Upload --}}
                            <div class="main-image-upload">
                                <div class="body-title mb-10">Main Image <span class="tf-color-1">*</span></div>
                                <label for="main-image-input" class="image-upload-box">
                                    <div class="upload-placeholder" id="main-image-placeholder" style="{{ $product->image ? 'display:none;' : '' }}">
                                        <i class="icon-image-plus icon"></i>
                                        <p>Click to upload</p>
                                    </div>
                                    <img src="{{ $product->image ? asset('uploads/products/'.$product->image) : '' }}" alt="Main image preview" class="preview" id="main-image-preview" style="{{ !$product->image ? 'display:none;' : '' }}">
                                </label>
                                <input type="file" name="image" id="main-image-input" accept="image/*" class="d-none">
                            </div>

                            {{-- Gallery Images --}}
                            <div>
                                <div class="body-title mb-10">Gallery Images</div>
                                <input type="file" name="images[]" id="gallery-images-input" accept="image/*" multiple class="d-none">
                                <div class="gallery-previews" id="gallery-preview-container">
                                    {{-- Existing Images --}}
                                    @if($product->images)
                                        @foreach(explode(',', $product->images) as $img)
                                        <div class="gallery-item" data-existing-image="{{ trim($img) }}">
                                            <img src="{{ asset('uploads/products/'.trim($img)) }}" alt="Gallery thumbnail">
                                            <button type="button" class="remove-image">&times;</button>
                                        </div>
                                        @endforeach
                                    @endif
                                    {{-- Add New Image Box --}}
                                    <label for="gallery-images-input" class="gallery-item" style="border: 2px dashed #dee2e6; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                                        <i class="icon-plus" style="font-size: 24px; color: #6c757d;"></i>
                                    </label>
                                </div>
                                <input type="hidden" name="removed_images" id="removed-images-input">
                            </div>
                        </div>

                        <hr style="margin: 24px 0;">

                        {{-- Other Fields --}}
                        <fieldset>
                            <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
                            <select name="category_id">
                                <option value="">Choose category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                        <fieldset>
                            <div class="body-title mb-10">Brand <span class="tf-color-1">*</span></div>
                            <select name="brand_id">
                                <option value="">Choose brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                        <fieldset>
                            <div class="body-title mb-10">SKU <span class="tf-color-1">*</span></div>
                            <input type="text" name="SKU" placeholder="Enter SKU" value="{{ old('SKU', $product->SKU) }}">
                        </fieldset>
                        <fieldset>
                            <div class="body-title mb-10">Stock Status</div>
                            <select name="stock_status">
                                <option value="instock" {{ $product->stock_status == 'instock' ? 'selected' : '' }}>In Stock</option>
                                <option value="outofstock" {{ $product->stock_status == 'outofstock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </fieldset>
                        <fieldset>
                            <div class="body-title mb-10">Featured</div>
                            <select name="featured">
                                <option value="0" {{ $product->featured == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $product->featured == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </fieldset>
                    </div>
                </div>
            </div>

            {{-- Floating Submit Button --}}
            <div class="floating-submit-container">
                <button type="submit" class="tf-button">Update Product</button>
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
                <button type="button" class="btn-remove-variant">&times;</button>
            </div>
        `;
        $block.append(newRow);
    });

    // Remove variant row
    $(document).on('click', '.btn-remove-variant', function(){
        $(this).closest('.variant-row').remove();
    });

    // Auto-slug from name
    $('input[name="name"]').on('change', function(){
        $('input[name="slug"]').val(StringToSlug($(this).val()));
    });

    function StringToSlug(Text) {
        return Text.toLowerCase()
            .replace(/[^\w ]+/g, "")
            .replace(/ +/g, "-");
    }

    // === SCRIPT PREVIEW & REMOVE GAMBAR ===
    // Main Image Preview
    $('#main-image-input').on('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#main-image-preview').attr('src', e.target.result).show();
                $('#main-image-placeholder').hide();
            }
            reader.readAsDataURL(file);
        }
    });

    // Gallery Images Preview
    $('#gallery-images-input').on('change', function(event) {
        const files = event.target.files;
        if (files) {
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const newImageHtml = `
                        <div class="gallery-item new-image">
                            <img src="${e.target.result}" alt="New gallery thumbnail">
                            <button type="button" class="remove-image">&times;</button>
                        </div>
                    `;
                    $(newImageHtml).insertBefore('label[for="gallery-images-input"]');
                }
                reader.readAsDataURL(files[i]);
            }
        }
        $(this).val('');
    });

    // Remove Image Logic
    let removedImages = [];
    $('#gallery-preview-container').on('click', '.remove-image', function() {
        const galleryItem = $(this).closest('.gallery-item');
        if (galleryItem.data('existing-image')) {
            removedImages.push(galleryItem.data('existing-image'));
            $('#removed-images-input').val(JSON.stringify(removedImages));
        }
        galleryItem.remove();
    });
    // === AKHIR SCRIPT GAMBAR ===

    // Pack variants to JSON on submit
    $('form').on('submit', function(){
        let variants = {};
        $('.variant-block').each(function(){
            let type = $(this).data('type');
            if (type === 'used' && !$('#hasUsedVariant').is(':checked')) {
                return;
            }
            let rows = [];
            $(this).find('.variant-row').each(function(){
                let size = $(this).find('input').eq(0).val();
                let regular_price = $(this).find('input').eq(1).val();
                let sale_price = $(this).find('input').eq(2).val();
                let quantity = $(this).find('input').eq(3).val();

                if(size || regular_price || sale_price || quantity){
                    rows.push({size, regular_price, sale_price, quantity});
                }
            });
            if(rows.length > 0) {
               variants[type] = rows;
            }
        });
        $('#variantsInput').val(JSON.stringify(variants));
    });
});
</script>
@endpush