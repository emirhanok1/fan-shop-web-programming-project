@extends('layouts.app')

@section('title', 'FanStore - Film & Dizi Merchandise')
@section('meta_description', 'Favori film ve dizilerinizin lisanslı merchandise ürünlerini keşfedin. Breaking Bad, Game of Thrones, Vikings ve daha fazlası.')

@section('content')

{{-- ===== HERO SECTION ===== --}}
<section class="position-relative overflow-hidden" style="background: var(--fs-hero-gradient); min-height: 460px;">
    <div class="container d-flex align-items-center" style="min-height: 460px;">
        <div class="row w-100 align-items-center">
            <div class="col-lg-7 py-5">
                <span class="badge rounded-pill px-3 py-2 mb-3" style="background: rgba(229,9,20,0.15); color: var(--fs-accent); font-weight: 600; font-size: 0.8rem;">
                    <i class="fas fa-star me-1"></i> Yeni Koleksiyon Yayında
                </span>
                <h1 class="display-4 fw-bold mb-3" style="line-height: 1.15;">
                    Favori Dizilerinizin<br>
                    <span style="color: var(--fs-accent);">Dünyasına Adım Atın</span>
                </h1>
                <p class="lead mb-4" style="color: var(--fs-text-muted); font-size: 1.1rem; max-width: 520px;">
                    Breaking Bad'den Game of Thrones'a, en sevilen franchise'ların lisanslı merchandise ürünlerini keşfedin.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('products.index') }}" class="btn btn-accent btn-lg px-4">
                        <i class="fas fa-shopping-bag me-2"></i>Alışverişe Başla
                    </a>
                    <a href="#featured" class="btn btn-accent-outline btn-lg px-4">
                        <i class="fas fa-compass me-2"></i>Koleksiyonu Keşfet
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-center align-items-center">
                <div class="position-relative">
                    <div style="width: 300px; height: 300px; border-radius: 50%; background: radial-gradient(circle, rgba(229,9,20,0.15) 0%, transparent 70%); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-film" style="font-size: 8rem; color: var(--fs-accent); opacity: 0.35;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Decorative gradient overlay --}}
    <div class="position-absolute bottom-0 start-0 w-100" style="height: 80px; background: linear-gradient(to top, var(--fs-bg), transparent);"></div>
</section>

{{-- ===== KATEGORİ BÖLÜMÜ ===== --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Kategoriler</h2>
            <p class="section-subtitle">İhtiyacınıza göre filtreleyin</p>
        </div>
        <div class="row g-3 justify-content-center">
            @php
                $categoryIcons = [
                    'figurler' => 'fa-chess-knight',
                    'posterler' => 'fa-image',
                    'kupalar' => 'fa-mug-hot',
                    'giysiler' => 'fa-tshirt',
                    'aksesuarlar' => 'fa-gem',
                ];
            @endphp
            @foreach($categories as $category)
                <div class="col-6 col-md-4 col-lg">
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="text-decoration-none">
                        <div class="fs-card text-center p-4" style="cursor: pointer;">
                            <div class="mb-3">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background: rgba(229,9,20,0.1);">
                                    <i class="fas {{ $categoryIcons[$category->slug] ?? 'fa-box' }} fa-lg" style="color: var(--fs-accent);"></i>
                                </div>
                            </div>
                            <h6 class="fw-bold mb-1" style="color: var(--fs-text);">{{ $category->name }}</h6>
                            <small style="color: var(--fs-text-muted);">{{ $category->products_count }} ürün</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== ÖNE ÇIKAN ÜRÜNLER ===== --}}
<section class="py-5" id="featured" style="background: var(--fs-card); border-top: 1px solid var(--fs-border); border-bottom: 1px solid var(--fs-border);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-0">Öne Çıkan Ürünler</h2>
                <p class="section-subtitle mb-0 mt-1">En popüler merchandise ürünleri</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-accent-outline d-none d-md-inline-flex">
                Tümünü Gör <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-3">
            @foreach($featuredProducts as $product)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="product-card h-100">
                        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
                            <img src="{{ asset($product->primary_image_path) }}" alt="{{ $product->name }}" loading="lazy">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <span class="franchise-badge">{{ $product->franchise }}</span>
                            <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
                                <h6 class="product-name">{{ $product->name }}</h6>
                            </a>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">{{ number_format($product->price, 2) }} ₺</span>
                                    @if($product->stock > 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">Stokta</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.7rem;">Tükendi</span>
                                    @endif
                                </div>
                                <a href="{{ route('products.show', $product->slug) }}" class="btn btn-accent btn-sm w-100 mt-2">
                                    <i class="fas fa-shopping-cart me-1"></i> Sepete Ekle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-4 d-md-none">
            <a href="{{ route('products.index') }}" class="btn btn-accent-outline">Tümünü Gör <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

{{-- ===== FRANCHISE BÖLÜMÜ ===== --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Franchise'a Göre Keşfet</h2>
            <p class="section-subtitle">Sevdiğiniz dizi veya filme göre ürünleri bulun</p>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            @foreach($franchises as $franchise)
                <a href="{{ route('products.index', ['franchise' => $franchise]) }}" class="btn btn-accent-outline px-3 py-2" style="font-size: 0.85rem;">
                    <i class="fas fa-film me-1"></i> {{ $franchise }}
                </a>
            @endforeach
        </div>
    </div>
</section>

@endsection
