@extends('layouts.app')

@section('title', 'Ödeme - FanStore')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 font-weight-bold"><i class="fas fa-credit-card text-danger me-2"></i>Sipariş Ödemesi</h1>
            <p class="text-muted">Lütfen teslimat adresi seçin ve ödeme işleminizi tamamlayın.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        
        <div class="row g-4">
            {{-- Left: Address and Payment Forms --}}
            <div class="col-lg-8">
                
                {{-- 1. Delivery Address --}}
                <div class="fs-card p-4 mb-4">
                    <h4 class="h5 mb-3 fw-bold"><i class="fas fa-map-marker-alt text-danger me-2"></i>1. Teslimat Adresi</h4>
                    <hr class="my-3" style="border-color: var(--fs-border);">

                    <div class="mb-4">
                        @if($addresses->isNotEmpty())
                            <div class="row g-3">
                                @foreach($addresses as $addr)
                                    <div class="col-md-6">
                                        <div class="card bg-transparent h-100 border p-3 address-selector-card {{ $addr->is_default ? 'border-danger' : 'border-secondary' }}" 
                                             style="cursor: pointer; border-color: var(--fs-border) !important;"
                                             data-address-id="{{ $addr->id }}"
                                             data-address-text="{{ $addr->full_address }}, {{ $addr->district }}/{{ $addr->city }}">
                                            <div class="d-flex align-items-start">
                                                <div class="form-check me-2">
                                                    <input class="form-check-input address-radio" type="radio" name="selected_address_id" 
                                                           id="addr-{{ $addr->id }}" value="{{ $addr->id }}" 
                                                           {{ $addr->is_default ? 'checked' : '' }}>
                                                </div>
                                                <div>
                                                    <label class="form-check-label fw-bold d-block" for="addr-{{ $addr->id }}" style="cursor: pointer;">
                                                        {{ $addr->title }}
                                                        @if($addr->is_default)
                                                            <span class="badge bg-danger ms-1 small" style="font-size: 0.65rem;">Varsayılan</span>
                                                        @endif
                                                    </label>
                                                    <small class="text-muted d-block mt-2">{{ $addr->full_address }}</small>
                                                    <small class="text-muted fw-semibold">{{ $addr->district }}/{{ $addr->city }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-check mt-3 pt-2 border-top" style="border-color: var(--fs-border) !important;">
                                <input class="form-check-input" type="radio" name="selected_address_id" id="addr-new" value="new" 
                                       {{ old('selected_address_id') === 'new' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="addr-new">
                                    Yeni Adres Gir
                                </label>
                            </div>
                        @else
                            <input type="hidden" name="selected_address_id" value="new">
                            <p class="text-muted small mb-2"><i class="fas fa-info-circle me-1"></i> Kayıtlı adresiniz bulunmadığı için yeni adres bilgilerinizi giriniz.</p>
                        @endif
                    </div>

                    {{-- Textarea for Address (hidden if registered address selected, shown for new address) --}}
                    <div class="mb-3 d-none" id="newAddressContainer">
                        <label for="shipping_address_textarea" class="form-label fw-semibold">Teslimat Adresi</label>
                        <textarea class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                  id="shipping_address_textarea" 
                                  rows="3" 
                                  placeholder="Lütfen tam adresinizi, mahalle, cadde, sokak, bina ve daire no olarak yazınız..."></textarea>
                    </div>

                    {{-- Target input that actually gets sent to backend --}}
                    <input type="hidden" name="shipping_address" id="final_shipping_address" value="{{ old('shipping_address') }}">

                    {{-- Google Maps --}}
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <div id="autocomplete-container" class="mb-3 mt-3"></div>
                    <div id="map-container"
                         style="height: 300px; border-radius: 8px;"
                         class="mb-3">
                    </div>
                </div>

                {{-- 2. Payment details --}}
                <div class="fs-card p-4">
                    <h4 class="h5 mb-3 fw-bold"><i class="fas fa-credit-card text-danger me-2"></i>2. Ödeme Bilgileri</h4>
                    <hr class="my-3" style="border-color: var(--fs-border);">

                    @if($balance_used > 0)
                        <div class="alert alert-success border-0 py-3 px-4 mb-4 d-flex align-items-center" style="background: rgba(25, 135, 84, 0.08); color: #198754;">
                            <i class="fas fa-wallet fa-lg me-3 text-success"></i>
                            <div>
                                <span class="fw-bold">Cüzdan Kullanımı:</span> Siparişinizin <strong class="fs-5">{{ number_format($balance_used, 2) }} ₺</strong>'lik kısmı mevcut hesap bakiyenizden tahsil edilecektir.
                            </div>
                        </div>
                    @endif

                    @if($card_amount > 0)
                        <p class="text-muted small mb-3">Kalan <strong class="text-danger">{{ number_format($card_amount, 2) }} ₺</strong> tutar için lütfen kart bilgilerinizi giriniz:</p>
                        
                        <div class="row g-3">
                            {{-- Card Number --}}
                            <div class="col-md-12">
                                <label for="card_number" class="form-label small fw-semibold">Kart Numarası</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-secondary text-muted"><i class="fas fa-credit-card"></i></span>
                                    <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                           id="card_number" name="card_number" placeholder="1234567812345678" 
                                           maxlength="16" value="{{ old('card_number') }}">
                                </div>
                            </div>
                            {{-- Expiry --}}
                            <div class="col-md-6">
                                <label for="card_expiry" class="form-label small fw-semibold">Son Kullanma Tarihi (AA/YY)</label>
                                <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                       id="card_expiry" name="card_expiry" placeholder="MM/YY" 
                                       maxlength="5" value="{{ old('card_expiry') }}">
                            </div>
                            {{-- CVV --}}
                            <div class="col-md-6">
                                <label for="card_cvv" class="form-label small fw-semibold">Güvenlik Kodu (CVV)</label>
                                <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                       id="card_cvv" name="card_cvv" placeholder="123" 
                                       maxlength="3" value="{{ old('card_cvv') }}">
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success border-0 py-3 px-4 mb-0 d-flex align-items-center" style="background: rgba(25, 135, 84, 0.08); color: #198754;">
                            <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                            <div>
                                Sipariş tutarının tamamı hesap bakiyeniz tarafından karşılanmaktadır. <strong class="fw-bold">Kredi kartı bilgisi girmenize gerek yoktur.</strong>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Right: Checkout Summary --}}
            <div class="col-lg-4">
                <div class="fs-card p-4 position-sticky" style="top: 100px;">
                    <h4 class="h5 mb-4 fw-bold">Sipariş Özeti</h4>
                    
                    {{-- Products list --}}
                    <div class="mb-4 max-height-300 overflow-y-auto">
                        @foreach($cart->items as $item)
                            <div class="d-flex align-items-center justify-content-between mb-3 small">
                                <div class="pe-2">
                                    <span class="fw-semibold d-block text-truncate" style="max-width: 180px;">{{ $item->product->name }}</span>
                                    <span class="text-muted">{{ $item->quantity }} adet</span>
                                </div>
                                <span class="fw-bold">{{ number_format($item->quantity * $item->product->price, 2) }} ₺</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <hr class="my-3" style="border-color: var(--fs-border);">
                    
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Ara Toplam</span>
                        <span class="fw-semibold">{{ number_format($total, 2) }} ₺</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Kargo</span>
                        <span class="text-success fw-semibold">Ücretsiz</span>
                    </div>

                    @if($balance_used > 0)
                        <div class="d-flex justify-content-between mb-2 small text-success">
                            <span>Bakiyeden Düşülen</span>
                            <span class="fw-semibold">-{{ number_format($balance_used, 2) }} ₺</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-4 pt-2 border-top" style="border-color: var(--fs-border) !important;">
                        <span class="fw-bold">Kredi Kartı ile Ödenecek</span>
                        <span class="h5 mb-0 fw-extrabold text-danger">{{ number_format($card_amount, 2) }} ₺</span>
                    </div>

                    <button type="submit" class="btn btn-accent w-100 py-3 fw-bold">
                        <i class="fas fa-check-circle me-2"></i>Siparişi Onayla ve Öde
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('cart.index') }}" class="text-muted small text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Sepete Geri Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
  const GOOGLE_MAPS_KEY = "{{ config('services.maps.key') }}";

  // FOUC önleme — key yoksa yükleme
  if (!GOOGLE_MAPS_KEY) {
    console.log('Google Maps API key bulunamadı');
  } else {
    // Asenkron Google Maps yükleyici
    (g=>{var h,a,k,p="The Google Maps JavaScript API",
    c="google",l="importLibrary",q="__ib__",m=document,
    b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),
    r=new Set,e=new URLSearchParams,
    u=()=>h||(h=new Promise(async(f,n)=>{
    await (a=m.createElement("script"));
    e.set("libraries",[...r]+"");
    for(k in g)e.set(k.replace(/[A-Z]/g,
    t=>"_"+t[0].toLowerCase()),g[k]);
    e.set("callback",c+".maps."+q);a.src=
    `https://maps.${c}apis.com/maps/api/js?`+e;
    d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));
    a.nonce=m.querySelector("script[nonce]")?.nonce||"";
    m.head.append(a)}));d[l]?console.warn(p+
    " only loads once. Ignoring:",g):d[l]=
    (f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))
    })({key: GOOGLE_MAPS_KEY, v: "beta", language: "tr", region: "TR"});

    async function initMap() {
      try {
        const { PlaceAutocompleteElement } =
          await google.maps.importLibrary("places");

        // Autocomplete elementi oluştur
        const autocomplete = new PlaceAutocompleteElement({
          includedRegionCodes: ['tr'],
        });
        autocomplete.id = "place-autocomplete";
        autocomplete.setAttribute("placeholder",
          "Adres arayın...");
        autocomplete.style.width = "100%";

        // Container'a ekle
        const container = document.getElementById(
          'autocomplete-container');
        if (container) container.appendChild(autocomplete);

        // Harita başlat
        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } =
          await google.maps.importLibrary("marker");

        const map = new Map(
          document.getElementById('map-container'), {
          center: { lat: 39.9334, lng: 32.8597 },
          zoom: 6,
          mapId: 'fanstore_map',
        });

        let marker = null;

        // Adres seçilince
        autocomplete.addEventListener('gmp-select',
          async ({ placePrediction }) => {
          if (!placePrediction) return;

          const place = placePrediction.toPlace();
          await place.fetchFields({
            fields: ['formattedAddress', 'location',
                     'addressComponents']
          });

          if (place.location) {
            // Haritayı güncelle
            map.setCenter(place.location);
            map.setZoom(15);

            if (marker) marker.map = null;
            marker = new AdvancedMarkerElement({
              map,
              position: place.location,
            });

            // Hidden input'ları doldur
            const addrInput = document.getElementById(
              'shipping_address_textarea');
            const latInput = document.getElementById(
              'latitude');
            const lngInput = document.getElementById(
              'longitude');

            if (addrInput) {
              addrInput.value = place.formattedAddress;
              addrInput.dispatchEvent(new Event('input'));
            }
            if (latInput)
              latInput.value = place.location.lat();
            if (lngInput)
              lngInput.value = place.location.lng();
          }
        });
      } catch(e) {
        console.warn('Google Maps yüklenemedi:', e);
      }
    }

    // DOM hazır olunca başlat
    document.addEventListener('DOMContentLoaded', initMap);
  }
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[name="selected_address_id"]');
    const newAddressContainer = document.getElementById('newAddressContainer');
    const shippingTextarea = document.getElementById('shipping_address_textarea');
    const finalShippingInput = document.getElementById('final_shipping_address');
    const addressCards = document.querySelectorAll('.address-selector-card');

    function updateAddressFields() {
        let selectedValue = 'new';
        const checkedRadio = document.querySelector('input[name="selected_address_id"]:checked');
        if (checkedRadio) {
            selectedValue = checkedRadio.value;
        }

        // Remove active border from all cards
        addressCards.forEach(c => {
            c.classList.remove('border-danger');
            c.classList.add('border-secondary');
            
            // Uncheck sub radio if not selected
            const radio = c.querySelector('.address-radio');
            if (radio && radio.value !== selectedValue) {
                radio.checked = false;
            }
        });

        if (selectedValue === 'new') {
            newAddressContainer.classList.remove('d-none');
            shippingTextarea.required = true;
            finalShippingInput.value = shippingTextarea.value;
        } else {
            newAddressContainer.classList.add('d-none');
            shippingTextarea.required = false;
            
            // Find chosen card and highlight
            const selectedCard = document.querySelector(`.address-selector-card[data-address-id="${selectedValue}"]`);
            if (selectedCard) {
                selectedCard.classList.remove('border-secondary');
                selectedCard.classList.add('border-danger');
                
                // Set final shipping input value
                finalShippingInput.value = selectedCard.getAttribute('data-address-text');
                
                // Select radio inside card
                const radio = selectedCard.querySelector('.address-radio');
                if (radio) radio.checked = true;
            }
        }
    }

    // Radio click
    radioButtons.forEach(radio => {
        radio.addEventListener('change', updateAddressFields);
    });

    // Card click selection
    addressCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Avoid double trigger if clicking radio itself
            if (e.target.tagName.toLowerCase() === 'input') return;
            
            const radio = this.querySelector('.address-radio');
            if (radio) {
                radio.checked = true;
                updateAddressFields();
            }
        });
    });

    // Textarea input syncs to hidden input
    shippingTextarea.addEventListener('input', function() {
        const checkedRadio = document.querySelector('input[name="selected_address_id"]:checked');
        if (checkedRadio && checkedRadio.value === 'new') {
            finalShippingInput.value = this.value;
        }
    });

    // Run on startup
    updateAddressFields();
});
</script>
@endpush
