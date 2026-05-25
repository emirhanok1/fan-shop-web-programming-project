@extends('layouts.app')

@section('title', ($activeCategory ? $activeCategory->name : (request('franchise') ? request('franchise') : 'Tüm Ürünler')) . ' - FanStore')

@section('content')
<div class="container py-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="section-title mb-0">
                @if($activeCategory)
                    {{ $activeCategory->name }}
                @elseif(request('franchise'))
                    {{ request('franchise') }}
                @elseif(request('search'))
                    "{{ request('search') }}" için sonuçlar
                @else
                    Tüm Ürünler
                @endif
            </h1>
            <p class="section-subtitle mb-0 mt-1">{{ $products->total() }} ürün bulundu</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- ===== FILTER SIDEBAR ===== --}}
        <div class="col-lg-3">
            <div class="fs-card p-3 mb-3">
                <h6 class="fw-bold mb-3"><i class="fas fa-filter me-2" style="color: var(--fs-accent);"></i>Filtreler</h6>
                <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                    {{-- Keep existing search --}}
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    {{-- Categories --}}
                    <div class="mb-4">
                        <label class="fw-semibold small mb-2 d-block" style="color: var(--fs-text-muted);">Kategori</label>
                        <div class="d-flex flex-column gap-1">
                            <label class="d-flex align-items-center gap-2 small" style="cursor:pointer;">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()">
                                <span>Tümü</span>
                            </label>
                            @foreach($categories as $cat)
                                <label class="d-flex align-items-center gap-2 small" style="cursor:pointer;">
                                    <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span>{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Franchise --}}
                    <div class="mb-4">
                        <label class="fw-semibold small mb-2 d-block" style="color: var(--fs-text-muted);">Franchise</label>
                        <select name="franchise" class="form-select form-select-sm fs-search-input" onchange="this.form.submit()">
                            <option value="">Tümü</option>
                            @foreach($franchises as $fr)
                                <option value="{{ $fr }}" {{ request('franchise') == $fr ? 'selected' : '' }}>{{ $fr }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Price Range --}}
                    <div class="mb-4">
                        <label class="fw-semibold small mb-2 d-block" style="color: var(--fs-text-muted);">Fiyat Aralığı</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="min_price" class="form-control form-control-sm fs-search-input" placeholder="Min" value="{{ request('min_price') }}" min="0">
                            <input type="number" name="max_price" class="form-control form-control-sm fs-search-input" placeholder="Max" value="{{ request('max_price') }}" min="0">
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div class="mb-4">
                        <label class="fw-semibold small mb-2 d-block" style="color: var(--fs-text-muted);">Sıralama</label>
                        <select name="sort" class="form-select form-select-sm fs-search-input" onchange="this.form.submit()">
                            <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>En Yeni</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Fiyat: Düşükten Yükseğe</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Fiyat: Yüksekten Düşüğe</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>En Popüler</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-accent btn-sm w-100 mb-2">
                        <i class="fas fa-search me-1"></i> Filtrele
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-accent-outline btn-sm w-100">Filtreleri Temizle</a>
                </form>
            </div>
        </div>

        {{-- ===== PRODUCT GRID ===== --}}
        <div class="col-lg-9">
            @if($products->count() > 0)
                <div class="row g-3">
                    @foreach($products as $product)
                        <div class="col-12 col-sm-6 col-xl-4">
                            <div class="product-card h-100">
                                <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
                                    <img src="{{ asset($product->primary_image_path) }}" alt="{{ $product->name }}" loading="lazy">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <span class="franchise-badge">{{ $product->franchise }}</span>
                                    <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
                                        <h6 class="product-name">{{ $product->name }}</h6>
                                    </a>
                                    <small style="color: var(--fs-text-muted); margin-bottom: 0.5rem;">
                                        <i class="fas fa-tag me-1"></i>{{ $product->category->name }}
                                    </small>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="product-price">{{ number_format($product->price, 2) }} ₺</span>
                                            @if($product->stock > 5)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">Stokta</span>
                                            @elseif($product->stock > 0)
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.7rem;">Son {{ $product->stock }} adet</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-accent btn-sm w-100">
                                            <i class="fas fa-eye me-1"></i> İncele
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x mb-3" style="color: var(--fs-text-muted);"></i>
                    <h5 class="fw-bold">Ürün bulunamadı</h5>
                    <p style="color: var(--fs-text-muted);">Farklı bir filtre veya arama terimi deneyin.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-accent-outline">Tüm Ürünleri Gör</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
