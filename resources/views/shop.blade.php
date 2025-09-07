@extends('layouts.app')

@section('title', 'Catalog')

@section('content')
<section class="catalog-page">
    <div class="catalog-container">

        <aside class="catalog-sidebar">
            <form method="GET" id="filterForm">
                <div class="filter-section">
                    <h4>PRODUCT CATEGORIES</h4>
                    <ul class="filter-list">
                        @foreach($categories as $cat)
                        <li>
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                {{ (is_array(request('categories')) && in_array($cat->id, request('categories'))) ? 'checked' : '' }}
                                onchange="document.getElementById('filterForm').submit();">
                            {{ $cat->name }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- CONDITION FILTER --}}
                <div class="filter-section">
                    <h4>CONDITION</h4>
                    <div class="size-options" id="conditionsContainer">
                        @foreach($conditions as $condition)
                        <button type="button"
                            class="filter-btn {{ in_array($condition, $f_conditions ?? []) ? 'active' : '' }}"
                            data-type="conditions" data-value="{{ $condition }}">
                            {{ ucfirst(str_replace('_', ' ', $condition)) }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- SIZE FILTER --}}
                <div class="filter-section">
                    <div class="filter-header" style="display:flex; justify-content:space-between; align-items:center;">
                        <h4 style="margin:0; padding-bottom:15px;">SIZES (US)</h4>
                        <button type="button" class="size-chart-btn" onclick="openSizeChart()">Size Chart</button>
                    </div>
                    <div class="size-options" id="sizesContainer">
                        @foreach($sizes as $sz)
                        <button type="button" class="filter-btn {{ in_array($sz, $f_sizes ?? []) ? 'active' : '' }}"
                            data-type="sizes" data-value="{{ $sz }}">
                            {{ $sz }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- BRANDS --}}
                <div class="filter-section">
                    <h4>BRANDS</h4>
                    <div class="brand-search">
                        <input type="text" placeholder="Search brand..." id="brandSearch" onkeyup="filterBrands()">
                    </div>
                    <ul class="filter-list brand-list" id="brandList">
                        @foreach($brands as $brand)
                        <li>
                            <input type="checkbox" name="brands[]" value="{{ $brand->id }}"
                                {{ (is_array(request('brands')) && in_array($brand->id, request('brands'))) ? 'checked' : '' }}
                                onchange="document.getElementById('filterForm').submit();">
                            {{ $brand->name }} <span>{{ $brand->products()->count() }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- PRICE --}}
                <div class="filter-section">
                    <h4>PRICE RANGE</h4>
                    <div class="price-range-inputs">
                        <div class="price-input-group">
                            <label for="minPrice">IDR Minimum Price</label>
                            <input type="number" name="min" id="minPrice" value="{{ request('min', $min_price) }}"
                                min="{{ $min_price }}" max="{{ $max_price }}" placeholder="Min">
                        </div>
                        <div class="price-input-group">
                            <label for="maxPrice">IDR Maximum Price</label>
                            <input type="number" name="max" id="maxPrice" value="{{ request('max', $max_price) }}"
                                min="{{ $min_price }}" max="{{ $max_price }}" placeholder="Max">
                        </div>
                        <button type="button" class="btn-apply" onclick="applyPriceFilter()">Apply</button>
                    </div>
                </div>
            </form>
        </aside>

        <div class="catalog-content">
            <h2>Catalog</h2>
            <div id="catalog-grid" class="catalog-grid">
                @foreach($products as $product)
                <div class="catalog-item">
                    <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                        <img loading="lazy" src="{{ asset('uploads/products/' . $product->image) }}" width="160"
                            height="130" alt="{{ $product->name }}" class="pc__img">
                    </a>
                    <div class="catalog-item-info">
                        <p class="item-name">{{ $product->name }}</p>
                        <div class="item-spacer"></div>
                        <p class="item-price">
                            IDR {{ number_format($product->min_variant_price) }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            <div id="infinite-loader" style="display:none; text-align:center; margin:24px 0;">
                <span>Loading...</span>
            </div>
        </div>
    </div>
</section>

{{-- SIZE CHART MODAL --}}
<div id="sizeChartModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="modal-close" onclick="closeSizeChart()">&times;</span>
        <h3>Size Conversion Chart</h3>
        <table class="size-chart-table">
            <thead>
                <tr>
                    <th>US</th>
                    <th>EUR</th>
                    <th>CM</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>4</td>
                    <td>36</td>
                    <td>22</td>
                </tr>
                <tr>
                    <td>4.5</td>
                    <td>36.5</td>
                    <td>22.5</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>37.5</td>
                    <td>23</td>
                </tr>
                <tr>
                    <td>5.5</td>
                    <td>38</td>
                    <td>23.5</td>
                </tr>
                <tr>
                    <td>6</td>
                    <td>38.5</td>
                    <td>24</td>
                </tr>
                <tr>
                    <td>6.5</td>
                    <td>39</td>
                    <td>24.5</td>
                </tr>
                <tr>
                    <td>7</td>
                    <td>40</td>
                    <td>25</td>
                </tr>
                <tr>
                    <td>7.5</td>
                    <td>40.5</td>
                    <td>25.5</td>
                </tr>
                <tr>
                    <td>8</td>
                    <td>41</td>
                    <td>26</td>
                </tr>
                <tr>
                    <td>8.5</td>
                    <td>42</td>
                    <td>26.5</td>
                </tr>
                <tr>
                    <td>9</td>
                    <td>42.5</td>
                    <td>27</td>
                </tr>
                <tr>
                    <td>9.5</td>
                    <td>43</td>
                    <td>27.5</td>
                </tr>
                <tr>
                    <td>10</td>
                    <td>44</td>
                    <td>28</td>
                </tr>
                <tr>
                    <td>10.5</td>
                    <td>44.5</td>
                    <td>28.5</td>
                </tr>
                <tr>
                    <td>11</td>
                    <td>45</td>
                    <td>29</td>
                </tr>
                <tr>
                    <td>11.5</td>
                    <td>46</td>
                    <td>29.5</td>
                </tr>
                <tr>
                    <td>12</td>
                    <td>46.5</td>
                    <td>30</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

{{-- Minimal CSS --}}
<style>
    .catalog-item {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 260px;
    }

    .catalog-item-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        height: 90px;
        /* atur agar tidak terlalu jauh, sesuaikan dengan kebutuhan */
        padding-top: 12px;
    }

    .item-name {
        min-height: 24px;
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 0;
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
    }

    .item-spacer {
        flex: 1;
        min-height: 12px;
        /* jarak antar nama dan harga, kecil saja */
    }

    .item-price {
        font-size: 18px;
        font-weight: 700;
        color: #19a74a;
        margin-bottom: 0;
        display: flex;
        align-items: flex-end;
        justify-content: flex-start;
    }

    .filter-section {
        margin-bottom: 32px;
    }

    .filter-section h4 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 12px;
        text-transform: uppercase;
    }

    .size-options {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-btn {
        background: #f3f3f3;
        border: none;
        padding: 6px 12px;
        font-size: 13px;
        cursor: pointer;
        border-radius: 6px;
        transition: background 0.15s ease, color 0.15s ease, transform 0.06s ease;
    }

    .filter-btn:hover {
        background: #e6e6e6;
        transform: translateY(-1px);
    }

    .filter-btn.active {
        background: #111;
        color: #fff;
    }

    .brand-search input {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .price-range-inputs input {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }

    .btn-apply {
        padding: 8px 12px;
        background-color: #000;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        margin-top: 5px;
    }

    /* Size chart button */
    .size-chart-btn {
        background: none;
        border: none;
        color: #0073e6;
        font-size: 13px;
        cursor: pointer;
        text-decoration: underline;
        padding: 0;
    }

    .size-chart-btn:hover {
        color: #004b99;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        padding: 40px 20px;
        /* biar gak mepet atas bawah */
        box-sizing: border-box;
    }

    .modal-content {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        /* batasi tinggi modal */
        overflow-y: auto;
        /* bikin isi bisa scroll */
        position: relative;
    }

    .modal-close {
        position: absolute;
        right: 12px;
        top: 8px;
        font-size: 24px;
        cursor: pointer;
    }

    .size-chart-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }

    .size-chart-table th,
    .size-chart-table td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: center;
    }

    .size-chart-table th {
        background: #f5f5f5;
    }

</style>

@push('scripts')
<script>
    // init arrays from PHP
    const initConditions = @json($f_conditions ?? []);
    const initSizes = @json($f_sizes ?? []);

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('filterForm');

        const selected = {
            conditions: new Set(initConditions),
            sizes: new Set(initSizes)
        };

        function syncHiddenInputs(type) {
            document.querySelectorAll('input[name="' + type + '[]"]').forEach(el => el.remove());
            selected[type].forEach(val => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = type + '[]';
                inp.value = val;
                form.appendChild(inp);
            });
        }

        syncHiddenInputs('conditions');
        syncHiddenInputs('sizes');

        function renderButtons() {
            document.querySelectorAll('.filter-btn[data-type="conditions"]').forEach(btn => {
                btn.classList.toggle('active', selected.conditions.has(btn.dataset.value));
            });
            document.querySelectorAll('.filter-btn[data-type="sizes"]').forEach(btn => {
                btn.classList.toggle('active', selected.sizes.has(btn.dataset.value));
            });
        }
        renderButtons();

        document.querySelectorAll('.filter-btn[data-type="conditions"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const v = btn.dataset.value;
                if (selected.conditions.has(v)) selected.conditions.delete(v);
                else selected.conditions.add(v);
                syncHiddenInputs('conditions');
                renderButtons();
                form.submit();
            });
        });

        document.querySelectorAll('.filter-btn[data-type="sizes"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const v = btn.dataset.value;
                if (selected.sizes.has(v)) selected.sizes.delete(v);
                else selected.sizes.add(v);
                syncHiddenInputs('sizes');
                renderButtons();
                form.submit();
            });
        });
    });

    function filterBrands() {
        let input = document.getElementById('brandSearch').value.toLowerCase();
        let items = document.querySelectorAll('#brandList li');
        items.forEach(li => {
            li.style.display = li.textContent.toLowerCase().includes(input) ? '' : 'none';
        });
    }

    function applyPriceFilter() {
        document.getElementById('filterForm').submit();
    }

    function openSizeChart() {
        document.getElementById('sizeChartModal').style.display = 'flex';
    }

    function closeSizeChart() {
        document.getElementById('sizeChartModal').style.display = 'none';
    }
    window.onclick = function (e) {
        const modal = document.getElementById('sizeChartModal');
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    }

</script>
<script>
let page = {{ $products->currentPage() }};
let lastPage = {{ $products->lastPage() }};
let loading = false;

window.addEventListener('scroll', function() {
    if (loading || page >= lastPage) return;
    const loader = document.getElementById('infinite-loader');
    const grid = document.getElementById('catalog-grid');
    if ((window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 300)) {
        loading = true;
        loader.style.display = 'block';
        fetch(`{{ request()->url() }}?{!! http_build_query(request()->except('page')) !!}&page=${page+1}`)
            .then(res => res.text())
            .then(html => {
                // Ambil hanya isi produk dari response
                let temp = document.createElement('div');
                temp.innerHTML = html;
                let newItems = temp.querySelectorAll('.catalog-item');
                newItems.forEach(item => grid.appendChild(item));
                page++;
                loader.style.display = 'none';
                loading = false;
            });
    }
});
</script>
@endpush
