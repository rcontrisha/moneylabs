<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>@yield('title', 'MoneyLab Sneakers')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    @stack('styles')
    <style>
        .icon-badge {
            position: relative;
            display: inline-block;
        }

        .icon-badge img {
            width: 28px;
            /* sesuaikan ukuran */
            height: auto;
        }

        .icon-badge .badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #e74c3c;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 50%;
            min-width: 20px;
            text-align: center;
        }

        /* Link text */
        .nav-links a {
            color: #222;
            text-decoration: none;
            padding: 6px 12px;
            transition: 0.2s;
        }

        .nav-links a:hover {
            color: #19a74a;
        }

        .nav-links a.active {
            font-weight: 600;
            color: #19a74a;
            border-bottom: 2px solid #19a74a;
        }

        /* Icon */
        .icon-badge.active img,
        .icons a.active img {
            filter: brightness(0) saturate(100%) invert(53%) sepia(88%) saturate(437%) hue-rotate(95deg) brightness(95%) contrast(95%);
        }

        /* Search icon wrapper (posisi absolut di dalam form relative) */
        .search-icon-wrapper{
            position: absolute;
            left: 12px;                 /* jarak dari tepi kiri input */
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;       /* klik melewati icon ke input */
            display: flex;
            align-items: center;
        }

        /* Space agar teks input tidak nabrak icon */
        .search-input{
            padding-left: 40px !important;        /* atur ruang untuk icon + gap */
            padding-inline-start: 40px !important;/* support RTL/browsers */
            box-sizing: border-box;
        }

        /* Pastikan parent form relative supaya ikon absolut posisinya benar */
        .search-form{ 
            position: relative; 
        }
    </style>
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">
                <img src="{{ asset('assets/images/logo_2.png') }}" alt="Logo">
                {{-- <span>MoneyLab Sneakers</span> --}}
            </div>
            <nav class="nav-links">
                <a href="{{route('home.index')}}"
                    class="{{ request()->routeIs('home.index') ? 'active' : '' }}">Home</a>
                <a href="{{route('shop.index')}}" class="{{ request()->routeIs('shop.*') ? 'active' : '' }}">Catalog</a>
                <a href="{{route('cart.index')}}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">Cart</a>
                <a href="{{route('shop.index')}}" class="{{ request()->is('about') ? 'active' : '' }}">About</a>
                <a href="{{route('shop.index')}}" class="{{ request()->is('contact') ? 'active' : '' }}">Contact</a>
            </nav>

            <div class="icons" style="display: flex; align-items: center; gap: 8px;">
                <!-- Search bar (selalu tampil) -->
                <div id="searchBar" class="ml-3" style="width: 16rem;">
                    <form action="{{ route('shop.index') }}" method="GET" class="search-form">
                        <!-- icon (absolutely positioned) -->
                        <div class="search-icon-wrapper" aria-hidden="true">
                            <svg class="" width="16" height="16" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="none">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>

                        <!-- input: tambahkan class search-input -->
                        <input
                            type="search"
                            id="default-search"
                            name="q"
                            value="{{ request('q') }}"
                            class="form-control search-input"
                            placeholder="Cari produk..."
                            aria-label="Cari produk"
                        />
                    </form>
                </div>

                {{-- Wishlist --}}
                <a href="{{ route('wishlist.index') }}" title="Wishlist"
                    class="icon-badge {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/icons/wishlist.png') }}" alt="Wishlist">
                    @if(\Cart::instance('wishlist')->count() > 0)
                    <span class="badge">{{ \Cart::instance('wishlist')->count() }}</span>
                    @endif
                </a>

                {{-- Profile --}}
                @auth
                @php
                $utype = Auth::user()->utype ?? 'USR';
                @endphp
                @if($utype === 'ADM')
                <a href="{{ route('admin.index') }}" title="Admin Dashboard"
                    style="display: flex; align-items: center; gap: 6px; text-decoration:none;">
                    <img src="{{ asset('assets/icons/profile.png') }}" alt="Admin">
                    <span style="font-size: 14px; color: #222; font-weight: 500;">
                        Admin
                    </span>
                </a>
                @else
                <a href="{{ route('user.index') }}" title="Account"
                    style="display: flex; align-items: center; gap: 6px; text-decoration:none;">
                    <img src="{{ asset('assets/icons/profile.png') }}" alt="User">
                    <span style="font-size: 14px; color: #222; font-weight: 500;">
                        {{ Auth::user()->name }}
                    </span>
                </a>
                @endif
                @else
                <a href="{{ route('login') }}" title="Login" style="display: flex; align-items: center; gap: 6px;">
                    <img src="{{ asset('assets/icons/profile.png') }}" alt="User">
                </a>
                @endauth

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}" title="Cart"
                    class="icon-badge {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/icons/cart.png') }}" alt="Cart">
                    @if(\Cart::instance('cart')->count() > 0)
                    <span class="badge">{{ \Cart::instance('cart')->count() }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer" role="contentinfo">
        <div class="footer-inner">
            <!-- LEFT: headline (2 lines) -->
            <div class="footer-col footer-left">
                <h1 class="headline" aria-hidden="true">
                    <span class="line">LETS DISCUSS</span>
                    <span class="line">WITH OUR TEAM</span>
                </h1>
            </div>

            <!-- MIDDLE: social icons + address in 2x2 grid -->
            <div class="footer-col footer-middle" aria-label="Contact & social">
                <!-- Social media icons (left side) -->
                <div class="socials-wrap" role="group" aria-label="Social links">
                    <div class="social-top" aria-hidden="true">
                        <a href="#" class="social-btn" aria-label="Instagram">
                            <img src="/images/logo/instagram.png" alt="Instagram">
                        </a>
                        <a href="#" class="social-btn" aria-label="Facebook">
                            <img src="/images/logo/facebook.png" alt="Facebook">
                        </a>
                    </div>

                    <div class="social-bottom" aria-hidden="true">
                        <a href="#" class="social-btn wa" aria-label="WhatsApp">
                            <img src="/images/logo/whatsapp.png" alt="WhatsApp">
                        </a>
                    </div>
                </div>

                <!-- Address sections (right side in 2 rows) -->
                <div class="address-section address-top">
                    <div class="addr-line">Ngringin, Condong Catur,</div>
                    <div class="addr-line">Depok, Sieman</div>
                </div>

                <div class="address-section address-bottom">
                    <div class="addr-line strong">Daerah Istimewa Yogyakarta</div>
                    <div class="addr-line strong">+62 895 2342 233</div>
                </div>
            </div>

            <!-- RIGHT: placeholder box -->
            <div class="footer-col footer-right">
                <div class="placeholder" aria-hidden="true"></div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById("default-search");
            const searchForm = searchInput.closest("form");

            // listen perubahan input
            searchInput.addEventListener("input", function() {
                if (this.value === "") {
                    searchForm.submit(); // submit otomatis biar tampil all product
                }
            });
        });
    </script>
    @stack("scripts")
</body>

</html>
