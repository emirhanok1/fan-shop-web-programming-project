<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FanStore - Film & Dizi Merchandise')</title>
    <meta name="description" content="@yield('meta_description', 'FanStore — Favori film ve dizilerinizin lisanslı ürünlerini keşfedin. Breaking Bad, Game of Thrones ve daha fazlası.')">

    {{-- FOUC Prevention: apply theme before anything renders --}}
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    {{-- Bootstrap 5.3 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    {{-- FontAwesome 5 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        :root {
            --fs-accent: #e50914;
            --fs-accent-hover: #b8070f;
            --fs-radius: 0.5rem;
        }

        [data-bs-theme="dark"] {
            --fs-bg: #141414;
            --fs-card: #1e1e1e;
            --fs-card-hover: #2a2a2a;
            --fs-text: #e5e5e5;
            --fs-text-muted: #8c8c8c;
            --fs-border: #2e2e2e;
            --fs-nav-bg: #0d0d0d;
            --fs-footer-bg: #0a0a0a;
            --fs-input-bg: #1e1e1e;
            --fs-hero-gradient: linear-gradient(135deg, #141414 0%, #1a0a0a 50%, #141414 100%);
        }

        [data-bs-theme="light"] {
            --fs-bg: #f8f9fa;
            --fs-card: #ffffff;
            --fs-card-hover: #f0f0f0;
            --fs-text: #1a1a1a;
            --fs-text-muted: #6c757d;
            --fs-border: #dee2e6;
            --fs-nav-bg: #ffffff;
            --fs-footer-bg: #1a1a1a;
            --fs-input-bg: #ffffff;
            --fs-hero-gradient: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background-color: var(--fs-bg);
            color: var(--fs-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        main { flex: 1; }

        /* --- Navbar --- */
        .fs-navbar {
            background-color: var(--fs-nav-bg) !important;
            border-bottom: 1px solid var(--fs-border);
            padding: 0.6rem 0;
            backdrop-filter: blur(12px);
            transition: background-color 0.3s ease;
        }
        .fs-navbar .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--fs-accent) !important;
            letter-spacing: -0.5px;
        }
        .fs-navbar .navbar-brand i { margin-right: 0.4rem; }
        .fs-navbar .nav-link {
            color: var(--fs-text) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 0.85rem !important;
            border-radius: var(--fs-radius);
            transition: all 0.2s ease;
        }
        .fs-navbar .nav-link:hover {
            color: var(--fs-accent) !important;
            background: rgba(229, 9, 20, 0.08);
        }

        /* --- Theme Toggle --- */
        .theme-toggle-btn {
            background: none;
            border: 1px solid var(--fs-border);
            color: var(--fs-text);
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .theme-toggle-btn:hover {
            border-color: var(--fs-accent);
            color: var(--fs-accent);
            transform: rotate(20deg);
        }

        /* --- Cart Badge --- */
        .cart-badge {
            position: absolute;
            top: -4px;
            right: -6px;
            background: var(--fs-accent);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- Buttons --- */
        .btn-accent {
            background-color: var(--fs-accent);
            border-color: var(--fs-accent);
            color: #fff;
            font-weight: 600;
            border-radius: var(--fs-radius);
            transition: all 0.25s ease;
        }
        .btn-accent:hover {
            background-color: var(--fs-accent-hover);
            border-color: var(--fs-accent-hover);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(229, 9, 20, 0.3);
        }
        .btn-accent-outline {
            background: transparent;
            border: 2px solid var(--fs-accent);
            color: var(--fs-accent);
            font-weight: 600;
            border-radius: var(--fs-radius);
            transition: all 0.25s ease;
        }
        .btn-accent-outline:hover {
            background-color: var(--fs-accent);
            color: #fff;
            transform: translateY(-1px);
        }

        /* --- Cards --- */
        .fs-card {
            background: var(--fs-card);
            border: 1px solid var(--fs-border);
            border-radius: var(--fs-radius);
            transition: all 0.3s ease;
        }
        .fs-card:hover {
            background: var(--fs-card-hover);
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        /* --- Footer --- */
        .fs-footer {
            background-color: var(--fs-footer-bg);
            border-top: 1px solid var(--fs-border);
            color: #999;
            padding: 3rem 0 1.5rem;
            transition: background-color 0.3s ease;
        }
        .fs-footer a {
            color: #bbb;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .fs-footer a:hover { color: var(--fs-accent); }
        .fs-footer h6 {
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        /* --- Product Card (Amazon-style horizontal) --- */
        .product-card {
            background: var(--fs-card);
            border: 1px solid var(--fs-border);
            border-radius: var(--fs-radius);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.15);
            border-color: var(--fs-accent);
        }
        .product-card img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
        }
        .product-card .card-body { padding: 1rem; }
        .product-card .product-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--fs-text);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 0.3rem;
        }
        .product-card .product-price {
            color: var(--fs-accent);
            font-weight: 800;
            font-size: 1.15rem;
        }
        .product-card .franchise-badge {
            display: inline-block;
            background: rgba(229, 9, 20, 0.12);
            color: var(--fs-accent);
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 3px;
            margin-bottom: 0.4rem;
        }

        /* --- Misc --- */
        .section-title {
            font-weight: 800;
            font-size: 1.6rem;
            margin-bottom: 0.3rem;
        }
        .section-subtitle {
            color: var(--fs-text-muted);
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }

        /* Dropdown menu theming */
        .dropdown-menu {
            background: var(--fs-card);
            border-color: var(--fs-border);
        }
        .dropdown-item {
            color: var(--fs-text);
            transition: all 0.2s;
        }
        .dropdown-item:hover {
            background: rgba(229, 9, 20, 0.08);
            color: var(--fs-accent);
        }

        /* Search bar */
        .fs-search-input {
            background: var(--fs-input-bg);
            border: 1px solid var(--fs-border);
            color: var(--fs-text);
            border-radius: var(--fs-radius);
        }
        .fs-search-input:focus {
            border-color: var(--fs-accent);
            box-shadow: 0 0 0 0.15rem rgba(229, 9, 20, 0.2);
            background: var(--fs-input-bg);
            color: var(--fs-text);
        }

        @stack('styles')
    </style>
</head>
<body>

{{-- ===== NAVBAR ===== --}}
<nav class="navbar navbar-expand-lg fs-navbar sticky-top">
    <div class="container">
        {{-- Brand --}}
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="fas fa-film"></i>FanStore
        </a>

        {{-- Mobile toggler --}}
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Menüyü Aç">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            {{-- Center links --}}
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Ana Sayfa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}">Ürünler</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Kategoriler
                    </a>
                    <ul class="dropdown-menu">
                        @php $navCategories = \App\Models\Category::all(); @endphp
                        @foreach($navCategories as $cat)
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </li>
            </ul>

            {{-- Right side --}}
            <div class="d-flex align-items-center gap-2">
                {{-- Search --}}
                <form action="{{ route('products.index') }}" method="GET" class="d-none d-lg-flex">
                    <input type="text" name="search" class="form-control form-control-sm fs-search-input" placeholder="Ürün ara..." style="width: 180px;" value="{{ request('search') }}">
                </form>

                {{-- Theme toggle --}}
                <button id="themeToggle" class="theme-toggle-btn" title="Tema Değiştir">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}" class="btn btn-sm position-relative" style="color: var(--fs-text);">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                    @if($cartCount > 0)
                        <span class="cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>

                @guest
                    <a href="{{ route('login') }}" class="btn btn-sm btn-accent-outline ms-1">Giriş Yap</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-accent ms-1">Kayıt Ol</a>
                @else
                    <div class="dropdown ms-1">
                        <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--fs-text); font-weight: 500;">
                            <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Panelim</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="fas fa-box me-2"></i>Siparişlerim</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>Profilim</a></li>
                            @if(Auth::user()->isAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-cog me-2"></i>Admin Panel</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
        </div>
    @endif
</div>

{{-- ===== MAIN CONTENT ===== --}}
<main>
    @yield('content')
</main>

{{-- ===== FOOTER ===== --}}
<footer class="fs-footer mt-auto">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="mb-3" style="color: var(--fs-accent); font-weight: 800;">
                    <i class="fas fa-film me-2"></i>FanStore
                </h5>
                <p class="small" style="line-height: 1.7;">
                    Film ve dizi dünyasının en sevilen ürünlerini keşfedin.
                    Breaking Bad'den Game of Thrones'a, koleksiyonunuzu tamamlayın.
                </p>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6>Hızlı Linkler</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('home') }}"><i class="fas fa-chevron-right me-1" style="font-size: 0.6rem;"></i> Ana Sayfa</a></li>
                    <li class="mb-2"><a href="{{ route('products.index') }}"><i class="fas fa-chevron-right me-1" style="font-size: 0.6rem;"></i> Ürünler</a></li>
                    <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-1" style="font-size: 0.6rem;"></i> Hakkımızda</a></li>
                    <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-1" style="font-size: 0.6rem;"></i> İletişim</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-12">
                <h6>İletişim</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@fanstore.com</li>
                    <li class="mb-2"><i class="fas fa-phone me-2"></i> +90 212 555 0000</li>
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> İstanbul, Türkiye</li>
                </ul>
            </div>
        </div>
        <hr style="border-color: var(--fs-border); opacity: 0.3;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <p class="small mb-0">&copy; {{ date('Y') }} <strong style="color: var(--fs-accent);">FanStore</strong>. Tüm hakları saklıdır.</p>
            <p class="small mb-0 mt-2 mt-md-0">Laravel 11 ile geliştirilmiştir.</p>
        </div>
    </div>
</footer>

{{-- Bootstrap 5.3 JS Bundle --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Theme Toggle Script --}}
<script>
    (function() {
        var themeBtn = document.getElementById('themeToggle');
        var themeIcon = document.getElementById('themeIcon');

        function updateIcon() {
            var current = document.documentElement.getAttribute('data-bs-theme');
            if (current === 'dark') {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            } else {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }
        }

        updateIcon();

        themeBtn.addEventListener('click', function() {
            var current = document.documentElement.getAttribute('data-bs-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            updateIcon();
        });
    })();
</script>

@stack('scripts')
</body>
</html>
