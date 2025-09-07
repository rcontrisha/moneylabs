@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="product-detail-page">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <a href="{{ route('home.index') }}">HOME</a>
    </nav>

    <div class="product-detail-container">
        <div class="product-detail__top">
            <!-- Product Gallery -->
            <div class="product-detail__gallery">
                <div class="thumbnails">
                    {{-- Gambar utama --}}
                    <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}"
                        class="thumbnail active"
                        onclick="changeMainImage('{{ asset('uploads/products/' . $product->image) }}', this)">
                    {{-- Gambar tambahan --}}
                    @if($product->images)
                    @foreach(explode(',', $product->images) as $img)
                    <img src="{{ asset('uploads/products/' . trim($img)) }}" alt="{{ $product->name }}"
                        class="thumbnail"
                        onclick="changeMainImage('{{ asset('uploads/products/' . trim($img)) }}', this)">
                    @endforeach
                    @endif
                </div>

                <div class="main-image">
                    <div class="image-navigation">
                        <button class="nav-arrow nav-prev" onclick="navigateImage(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <img id="mainImage" src="{{ asset('uploads/products/' . $product->image) }}"
                            alt="{{ $product->name }}">
                        <button class="nav-arrow nav-next" onclick="navigateImage(1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-detail__info">
                <div class="product-title-wrapper" style="display:flex; justify-content:space-between; gap:12px; padding-bottom:16px">
                    <h1 class="product-title" style="margin:0; font-size:1.8rem; font-weight:600;">
                        {{ $product->name }}
                    </h1>

                    @php
                        $inWishlist = \Surfsidemedia\Shoppingcart\Facades\Cart::instance('wishlist')
                            ->content()->where('id', $product->id)->first();
                    @endphp

                    <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}" style="margin:0;">
                        @csrf
                        <button type="submit" style="background:none; border:none; cursor:pointer; font-size:1.6rem; line-height:1; padding:4px;">
                            @if($inWishlist)
                                <i class="fa-solid fa-heart" style="color:#e74c3c;"></i>
                            @else
                                <i class="fa-regular fa-heart" style="color:#999;"></i>
                            @endif
                        </button>
                    </form>
                </div>

                <!-- Tombol Back -->
                <button type="button" id="backToInfo" class="btn-back" style="display:none;">
                    <i class="fas fa-arrow-left"></i>
                </button>

                <div id="infoContent"></div>

                <!-- Add to Cart -->
                <form id="cartForm" style="display:none;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="name" value="{{ $product->name }}">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="price" id="selectedPrice">
                    <input type="hidden" name="size" id="selectedSize">
                    <input type="hidden" name="condition" id="selectedCondition">
                    <button type="submit" class="btn-add-cart fullwidth">Add to Cart</button>
                </form>
                <div id="cartMessage" style="margin-top:10px;"></div>

                <!-- Modal Popup -->
                <div id="cartModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.35);">
                    <div style="position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); background:#fff; padding:32px 24px; border-radius:12px; box-shadow:0 2px 24px rgba(0,0,0,0.18); min-width:280px; max-width:90vw; text-align:center;">
                        <div id="cartModalContent" style="font-size:1.1em;"></div>
                        <button id="closeCartModal" style="margin-top:18px; padding:8px 24px; border-radius:6px; border:none; background:#000; color:#fff; font-weight:600; cursor:pointer;">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="related-products">
            <h3 class="related-title">RELATED <span class="highlight">PRODUCTS</span></h3>
            <div class="related-grid" id="relatedGrid">
                @foreach($related as $item)
                <div class="related-item">
                    <a href="{{ route('shop.product.details', ['product_slug' => $item->slug]) }}" class="related-link">
                        <div class="related-image">
                            <img src="{{ asset('uploads/products/' . $item->image) }}" alt="{{ $item->name }}">
                        </div>
                        <div class="related-info">
                            <p class="related-name">{{ $item->name }}</p>
                            <p class="related-price">IDR {{ number_format($item->min_variant_price, 0, ',', '.') }}</p>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @if($relatedAll->count() > 4)
            <div style="text-align:center; margin-top:18px;">
                <button id="viewMoreRelated" class="btn-view-more" style="padding:8px 24px; border-radius:6px; border:none; background:#000; color:#fff; font-weight:600; cursor:pointer;">
                    View More
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script>
    const allVariants = @json($allVariants);
    const infoContent = document.getElementById("infoContent");
    const backBtn = document.getElementById("backToInfo");

    // fungsi helper buat render dengan animasi
    function renderWithAnimation(html) {
        infoContent.innerHTML = `<div class="info-animate">${html}</div>`;
        const wrapper = infoContent.querySelector(".info-animate");

        // kasih delay biar transisi jalan
        requestAnimationFrame(() => {
            wrapper.classList.add("show");
        });
    }

    // render default content
    function renderDefaultContent() {
        let html = `
        <div class="info-default">
            <div class="price-section">
                <div class="start-from">Start from</div>
                <div class="sale-price">IDR {{ number_format($minPrice, 0, ',', '.') }}</div>
            </div>

            <hr><p class="product-description">{{ $product->description }}</p><hr>

            <div class="product-meta-horizontal">
                <div class="meta-item">
                    <span class="meta-label">SKU</span>
                    <span class="meta-value">{{ $product->SKU }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Category</span>
                    <span class="meta-value">{{ $product->category->name }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Brand</span>
                    <span class="meta-value">{{ $product->brand->name }}</span>
                </div>
            </div>

            <div class="variant-selection-inline">
                <button type="button" class="variant-btn" data-condition="brand_new">Brand New</button>
                <button type="button" class="variant-btn" data-condition="used">Used</button>
            </div>
        </div>
    `;
        renderWithAnimation(html);
        bindVariantButtons(infoContent);
    }

    // bind varian & tabel
    function bindVariantButtons(scope) {
        scope.querySelectorAll('.variant-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const condition = this.dataset.condition;
                scope.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                document.getElementById('selectedCondition').value = condition;
                const filtered = allVariants.filter(v => v.condition === condition);

                if (filtered.length === 0) {
                    renderWithAnimation('<p>Tidak ada varian tersedia</p>');
                    return;
                }

                // build tabel varian
                let html = `
                <div class="variant-selection-inline">
                    <button type="button" class="variant-btn ${condition === 'brand_new' ? 'active' : ''}" data-condition="brand_new">Brand New</button>
                    <button type="button" class="variant-btn ${condition === 'used' ? 'active' : ''}" data-condition="used">Used</button>
                </div>
                <table class="size-table">
                    <thead>
                        <tr>
                            <th>Price</th>
                            <th>US Size</th>
                            <th>Highest Offer</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
                filtered.forEach(item => {
                    html += `
                    <tr class="size-row" data-size="${item.size}" data-price="${item.sale_price}">
                        <td class="price">IDR ${parseInt(item.sale_price).toLocaleString('id-ID')}</td>
                        <td>${item.size}</td>
                        <td>-</td>
                    </tr>
                `;
                });
                html += `</tbody></table>`;

                renderWithAnimation(html);
                backBtn.style.display = "inline-flex";

                // re-bind lagi
                bindVariantButtons(infoContent);
                infoContent.querySelectorAll('.size-row').forEach(row => {
                    row.addEventListener('click', function () {
                        infoContent.querySelectorAll('.size-row').forEach(r => r
                            .classList.remove('selected'));
                        this.classList.add('selected');
                        document.getElementById('selectedSize').value = this.dataset
                            .size;
                        document.getElementById('selectedPrice').value = this.dataset
                            .price;
                        document.getElementById('cartForm').style.display = 'block';
                    });
                });
            });
        });
    }

    // tombol back
    backBtn.addEventListener("click", () => {
        renderDefaultContent();
        backBtn.style.display = "none";
        document.getElementById('cartForm').style.display = 'none';
    });

    // === GALLERY NAV ===
    const mainImageEl = document.getElementById('mainImage');
    const thumbElements = document.querySelectorAll('.thumbnails img');

    // Kumpulin semua URL gambar dari thumbnail (termasuk yang utama)
    const productImages = Array.from(thumbElements).map(img => img.getAttribute('src'));

    // index gambar aktif (default: 0 = gambar pertama)
    let currentImageIndex = 0;

    // Klik thumbnail -> ganti gambar + update index + kelas aktif
    window.changeMainImage = function (imageSrc, thumbnailElement) {
        mainImageEl.src = imageSrc;
        currentImageIndex = productImages.findIndex(src => src === imageSrc);

        document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
        if (thumbnailElement) thumbnailElement.classList.add('active');
    };

    // Panah kiri/kanan
    window.navigateImage = function (direction) {
        if (!productImages.length) return;

        currentImageIndex = (currentImageIndex + direction + productImages.length) % productImages.length;
        const newSrc = productImages[currentImageIndex];
        mainImageEl.src = newSrc;

        // sinkronkan highlight thumbnail
        document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
            thumb.classList.toggle('active', i === currentImageIndex);
        });
    };

    // Opsional: pastikan index awal sinkron bila main image berbeda
    currentImageIndex = productImages.findIndex(src => src === mainImageEl.getAttribute('src'));
    if (currentImageIndex < 0) currentImageIndex = 0;

    // init
    renderDefaultContent();

    // === ADD TO CART AJAX ===
    document.getElementById('cartForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);

        fetch("{{ route('cart.add') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const modal = document.getElementById('cartModal');
            const modalContent = document.getElementById('cartModalContent');
            if (data.status === 200) {
                modalContent.innerHTML = `<div style="color:green;"><i class="fas fa-check-circle" style="font-size:2em;"></i><br>${data.message}</div>`;
                form.style.display = 'none';
            } else {
                modalContent.innerHTML = `<div style="color:red;"><i class="fas fa-times-circle" style="font-size:2em;"></i><br>Gagal menambah ke cart</div>`;
            }
            modal.style.display = 'block';
        })
        .catch(() => {
            const modal = document.getElementById('cartModal');
            const modalContent = document.getElementById('cartModalContent');
            modalContent.innerHTML = `<div style="color:red;"><i class="fas fa-times-circle" style="font-size:2em;"></i><br>Terjadi kesalahan.</div>`;
            modal.style.display = 'block';
        });
    });

    // Tutup modal saat klik tombol OK
    document.getElementById('closeCartModal').onclick = function() {
        document.getElementById('cartModal').style.display = 'none';
    };

    document.querySelectorAll("form[action*='wishlist/toggle']").forEach(form => {
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            fetch(this.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": this.querySelector("[name=_token]").value,
                    "Accept": "application/json"
                }
            }).then(() => location.reload());
        });
    });
    </script>
    @if($relatedAll->count() > 4)
    @php
        $moreRelatedHtml = '';
        foreach($relatedAll->slice(4) as $item) {
            $moreRelatedHtml .= '
            <div class="related-item">
                <a href="' . route('shop.product.details', ['product_slug' => $item->slug]) . '" class="related-link">
                    <div class="related-image">
                        <img src="' . asset('uploads/products/' . $item->image) . '" alt="' . $item->name . '">
                    </div>
                    <div class="related-info">
                        <p class="related-name">' . $item->name . '</p>
                        <p class="related-price">IDR ' . number_format($item->min_variant_price, 0, ',', '.') . '</p>
                    </div>
                </a>
            </div>
            ';
        }
    @endphp
    <script>
        const moreRelatedHtml = `{!! $moreRelatedHtml !!}`;
        document.getElementById('viewMoreRelated').onclick = function() {
            const grid = document.getElementById('relatedGrid');
            grid.innerHTML += moreRelatedHtml;
            this.style.display = 'none';
        }
    </script>
    @endif
</script>
@endpush
