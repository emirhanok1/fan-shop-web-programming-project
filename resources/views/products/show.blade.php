@extends('layouts.app')

@section('title', $product->name . ' - FanStore')
@section('meta_description', $product->description ? \Illuminate\Support\Str::limit($product->description, 160) : $product->name . ' - FanStore')

@section('content')
<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small" style="--bs-breadcrumb-divider: '›';">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: var(--fs-text-muted); text-decoration: none;">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" style="color: var(--fs-text-muted); text-decoration: none;">Ürünler</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category->slug]) }}" style="color: var(--fs-text-muted); text-decoration: none;">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: var(--fs-text);">{{ $product->name }}</li>
        </ol>
    </nav>

    {{-- ===== PRODUCT DETAIL ===== --}}
    <div class="row g-4">
        {{-- Image Gallery --}}
        <div class="col-lg-5">
            <div class="fs-card p-3">
                {{-- Main image --}}
                <div class="mb-3 text-center" style="border-radius: var(--fs-radius); overflow: hidden; background: #0a0a0a;">
                    <img id="mainProductImage"
                         src="{{ asset($product->primary_image_path) }}"
                         alt="{{ $product->name }}"
                         style="width: 100%; max-height: 450px; object-fit: contain;">
                </div>
                {{-- Thumbnails --}}
                @if($product->productImages->count() > 1)
                    <div class="d-flex gap-2 flex-wrap justify-content-center">
                        @foreach($product->productImages as $img)
                            <div class="product-thumbnail {{ $img->is_primary ? 'active' : '' }}"
                                 onclick="document.getElementById('mainProductImage').src='{{ asset($img->image_path) }}'; document.querySelectorAll('.product-thumbnail').forEach(t=>t.classList.remove('active')); this.classList.add('active');"
                                 style="width: 65px; height: 65px; border-radius: 6px; overflow: hidden; cursor: pointer; border: 2px solid {{ $img->is_primary ? 'var(--fs-accent)' : 'var(--fs-border)' }}; transition: border-color 0.2s;">
                                <img src="{{ asset($img->image_path) }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-lg-7">
            <div class="fs-card p-4">
                {{-- Franchise badge --}}
                <a href="{{ route('products.index', ['franchise' => $product->franchise]) }}" class="text-decoration-none">
                    <span class="franchise-badge mb-2" style="font-size: 0.8rem; padding: 0.3rem 0.7rem;">
                        <i class="fas fa-film me-1"></i> {{ $product->franchise }}
                    </span>
                </a>

                <h1 class="fw-bold mb-2" style="font-size: 1.6rem;">{{ $product->name }}</h1>

                {{-- Category --}}
                <p class="small mb-3" style="color: var(--fs-text-muted);">
                    <i class="fas fa-tag me-1"></i>
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" style="color: var(--fs-text-muted); text-decoration: none;">{{ $product->category->name }}</a>
                </p>

                {{-- Price --}}
                <div class="mb-3">
                    <span style="color: var(--fs-accent); font-size: 2rem; font-weight: 800;">{{ number_format($product->price, 2) }} ₺</span>
                </div>

                {{-- Stock --}}
                <div class="mb-3">
                    @if($product->stock > 10)
                        <span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i> Stokta</span>
                    @elseif($product->stock > 0)
                        <span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-exclamation-circle me-1"></i> Son {{ $product->stock }} adet</span>
                    @else
                        <span class="badge bg-danger px-3 py-2"><i class="fas fa-times-circle me-1"></i> Tükendi</span>
                    @endif
                </div>

                {{-- Description --}}
                @if($product->description)
                    <div class="mb-4" style="color: var(--fs-text-muted); line-height: 1.7;">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                @endif

                <hr style="border-color: var(--fs-border);">

                {{-- Add to Cart Form --}}
                @if($product->stock > 0)
                    <form id="addToCartForm" action="{{ route('cart.add') }}" method="POST" class="d-flex align-items-end gap-3 flex-wrap">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div>
                            <label class="form-label small fw-semibold" style="color: var(--fs-text-muted);">Adet</label>
                            <select name="quantity" class="form-select fs-search-input" style="width: 80px;">
                                @for($i = 1; $i <= min($product->stock, 10); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit" class="btn btn-accent btn-lg px-5">
                            <i class="fas fa-shopping-cart me-2"></i> Sepete Ekle
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary btn-lg px-5" disabled>
                        <i class="fas fa-ban me-2"></i> Stokta Yok
                    </button>
                @endif
            </div>

            {{-- TMDB Info --}}
            @if(isset($tmdbData) && $tmdbData)
            <div class="tmdb-section mt-4 p-4 rounded"
                 style="background: var(--fs-card)">

              <div class="row align-items-start">
                {{-- Poster --}}
                <div class="col-md-3 mb-3">
                  @php
                    $posterUrl = app(App\Services\TMDBService::class)
                        ->getPosterUrl($tmdbData['poster_path'] ?? null);
                  @endphp
                  @if($posterUrl)
                    <img src="{{ $posterUrl }}"
                         class="img-fluid rounded shadow"
                         alt="{{ $product->franchise }}">
                  @endif
                </div>

                {{-- Bilgiler --}}
                <div class="col-md-9">
                  <h4 class="fw-bold mb-1">
                    {{ app(App\Services\TMDBService::class)
                        ->getTitle($tmdbData, $product->tmdb_type) }}
                  </h4>

                  {{-- Tür badge'leri --}}
                  <div class="mb-2">
                    @foreach($tmdbData['genres'] ?? [] as $genre)
                      <span class="badge bg-secondary me-1">
                        {{ $genre['name'] }}
                      </span>
                    @endforeach
                  </div>

                  {{-- Puan --}}
                  @if(isset($tmdbData['vote_average']))
                  <div class="mb-2">
                    <span class="text-warning fs-5">
                      @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor(app(App\Services\TMDBService::class)->getStarRating($tmdbData['vote_average'])))
                          ★
                        @else
                          ☆
                        @endif
                      @endfor
                    </span>
                    <span class="ms-1 text-muted">
                      {{ number_format($tmdbData['vote_average'], 1) }}/10
                      ({{ number_format($tmdbData['vote_count'] ?? 0) }} oy)
                    </span>
                  </div>
                  @endif

                  {{-- Yayın tarihi --}}
                  @if(isset($tmdbData['first_air_date']) || isset($tmdbData['release_date']))
                  <p class="text-muted small mb-2">
                    📅 İlk Yayın:
                    {{ $tmdbData['first_air_date']
                       ?? $tmdbData['release_date'] ?? '' }}
                  </p>
                  @endif

                  {{-- Özet --}}
                  @if(!empty($tmdbData['overview']))
                  <p class="mb-0" style="font-size: 0.9rem; line-height: 1.6">
                    {{ Str::limit($tmdbData['overview'], 300) }}
                  </p>
                  @endif
                </div>
              </div>
            </div>
            @else
            <div class="text-muted small mt-3">
              <i class="fas fa-film me-1"></i>
              Bu franchise hakkında bilgi yüklenemedi.
            </div>
            @endif
        </div>
    </div>

    {{-- ===== İLGİLİ ÜRÜNLER ===== --}}
    @if($relatedProducts->count() > 0)
        <section class="mt-5 pt-4" style="border-top: 1px solid var(--fs-border);">
            <h4 class="section-title mb-3">Aynı Franchise'dan Ürünler</h4>
            <div class="row g-3">
                @foreach($relatedProducts as $related)
                    <div class="col-6 col-md-3">
                        <div class="product-card h-100">
                            <a href="{{ route('products.show', $related->slug) }}" class="text-decoration-none">
                                <img src="{{ asset($related->primary_image_path) }}" alt="{{ $related->name }}" loading="lazy">
                            </a>
                            <div class="card-body">
                                <span class="franchise-badge">{{ $related->franchise }}</span>
                                <a href="{{ route('products.show', $related->slug) }}" class="text-decoration-none">
                                    <h6 class="product-name">{{ $related->name }}</h6>
                                </a>
                                <span class="product-price">{{ number_format($related->price, 2) }} ₺</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartForm = document.getElementById('addToCartForm');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Ekleniyor...';
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(result => {
                const data = result.body;
                if(result.status === 200 && data.success) {
                    // Update badge
                    let cartBtn = document.querySelector('a[href="{{ route(\'cart.index\') }}"]');
                    if (cartBtn) {
                        let badge = cartBtn.querySelector('.cart-badge');
                        if(!badge) {
                            badge = document.createElement('span');
                            badge.className = 'cart-badge';
                            cartBtn.appendChild(badge);
                        }
                        badge.innerText = data.cartCount;
                    }
                    alert(data.message || 'Ürün sepete eklendi!');
                } else {
                    alert(data.message || 'Bir hata oluştu.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Sunucu ile bağlantı kurulamadı.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
});
</script>
@endpush

@endsection
