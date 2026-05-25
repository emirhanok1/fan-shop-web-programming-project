@extends('layouts.app')

@section('title', 'Adreslerim - FanStore')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 font-weight-bold"><i class="fas fa-map-marker-alt text-danger me-2"></i>Adres Defterim</h1>
            <p class="text-muted">Siparişlerinizde kullanmak üzere teslimat adreslerinizi yönetebilirsiniz.</p>
        </div>
        <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end">
            <button type="button" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                <i class="fas fa-plus-circle me-2"></i>Yeni Adres Ekle
            </button>
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

    <div class="row g-4">
        @if($addresses->isEmpty())
            <div class="col-12">
                <div class="text-center py-5 fs-card p-5">
                    <div class="mb-3">
                        <i class="fas fa-map-marked fa-3x text-muted opacity-50"></i>
                    </div>
                    <h5 class="fw-semibold">Henüz kayıtlı bir adresiniz bulunmuyor.</h5>
                    <p class="text-muted mb-4">Sipariş adımlarını hızlandırmak için hemen ilk adresinizi tanımlayın.</p>
                    <button type="button" class="btn btn-accent-outline" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                        İlk Adresimi Ekle
                    </button>
                </div>
            </div>
        @else
            @foreach($addresses as $addr)
                <div class="col-md-6" id="address-card-{{ $addr->id }}">
                    <div class="fs-card p-4 h-100 d-flex flex-column justify-content-between position-relative">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-map-pin text-danger me-2"></i>{{ $addr->title }}
                                </h5>
                                <div>
                                    @if($addr->is_default)
                                        <span class="badge bg-danger">Varsayılan</span>
                                    @endif
                                </div>
                            </div>
                            <p class="small text-muted mb-3" style="line-height: 1.6;">
                                {{ $addr->full_address }}<br>
                                <strong>{{ $addr->district ?? '' }} / {{ $addr->city }}</strong>
                                @if($addr->zip)
                                    <span class="ms-1">({{ $addr->zip }})</span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" style="border-color: var(--fs-border) !important;">
                            <div>
                                @if(!$addr->is_default)
                                    <button class="btn btn-sm btn-link text-success p-0 fw-semibold text-decoration-none btn-set-default" 
                                            data-id="{{ $addr->id }}">
                                        <i class="fas fa-star me-1"></i>Varsayılan Yap
                                    </button>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-secondary py-1 px-2 border-secondary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editAddressModal-{{ $addr->id }}"
                                        style="color: var(--fs-text);">
                                    <i class="fas fa-edit"></i> Düzenle
                                </button>
                                <button class="btn btn-sm btn-outline-danger py-1 px-2 border-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteAddressModal-{{ $addr->id }}">
                                    <i class="fas fa-trash"></i> Sil
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Edit Address Modal --}}
                <div class="modal fade" id="editAddressModal-{{ $addr->id }}" tabindex="-1" aria-labelledby="editAddressModalLabel-{{ $addr->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content bg-dark text-white border-secondary">
                            <form action="{{ route('addresses.update', $addr) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-secondary">
                                    <h5 class="modal-title fw-bold" id="editAddressModalLabel-{{ $addr->id }}">Adresi Düzenle</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label for="title-{{ $addr->id }}" class="form-label small fw-semibold">Adres Başlığı (Örn: Ev, İş)</label>
                                            <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                                   id="title-{{ $addr->id }}" name="title" value="{{ $addr->title }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="city-{{ $addr->id }}" class="form-label small fw-semibold">Şehir</label>
                                            <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                                   id="city-{{ $addr->id }}" name="city" value="{{ $addr->city }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="district-{{ $addr->id }}" class="form-label small fw-semibold">İlçe</label>
                                            <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                                   id="district-{{ $addr->id }}" name="district" value="{{ $addr->district }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label for="full_address-{{ $addr->id }}" class="form-label small fw-semibold">Tam Adres</label>
                                            <textarea class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                                      id="full_address-{{ $addr->id }}" name="full_address" rows="3" required>{{ $addr->full_address }}</textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="zip-{{ $addr->id }}" class="form-label small fw-semibold">Posta Kodu</label>
                                            <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                                   id="zip-{{ $addr->id }}" name="zip" value="{{ $addr->zip }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-secondary">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-accent btn-sm">Güncelle</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Delete Confirm Modal --}}
                <div class="modal fade" id="deleteAddressModal-{{ $addr->id }}" tabindex="-1" aria-labelledby="deleteAddressModalLabel-{{ $addr->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content bg-dark text-white border-secondary">
                            <div class="modal-header border-secondary py-2">
                                <h6 class="modal-title fw-bold" id="deleteAddressModalLabel-{{ $addr->id }}">Adresi Sil?</h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <i class="fas fa-trash-alt fa-2x text-danger mb-2"></i>
                                <p class="mb-0 small fw-semibold">"{{ $addr->title }}" adresini silmek istediğinize emin misiniz?</p>
                            </div>
                            <div class="modal-footer border-secondary py-2 justify-content-between">
                                <button type="button" class="btn btn-secondary btn-xs" data-bs-dismiss="modal">İptal</button>
                                <form action="{{ route('addresses.destroy', $addr) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs">Sil</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

{{-- Add Address Modal --}}
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <form action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold" id="addAddressModalLabel">Yeni Adres Ekle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="title" class="form-label small fw-semibold">Adres Başlığı (Örn: Ev, İş)</label>
                            <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                   id="title" name="title" placeholder="Evim" required>
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label small fw-semibold">Şehir</label>
                            <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                   id="city" name="city" placeholder="İstanbul" required>
                        </div>
                        <div class="col-md-6">
                            <label for="district" class="form-label small fw-semibold">İlçe</label>
                            <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                   id="district" name="district" placeholder="Kadıköy">
                        </div>
                        <div class="col-md-12">
                            <label for="full_address" class="form-label small fw-semibold">Tam Adres</label>
                            <textarea class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                      id="full_address" name="full_address" rows="3" placeholder="Örn: Caferağa Mah. Moda Cad. No:1 D:2" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="zip" class="form-label small fw-semibold">Posta Kodu</label>
                            <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                   id="zip" name="zip" placeholder="34710">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-accent btn-sm">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default click event
    document.querySelectorAll('.btn-set-default').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            fetch(`/addresses/${id}/default`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload page to update default badges
                } else {
                    alert('İşlem başarısız oldu.');
                }
            })
            .catch(err => {
                alert('Sunucuyla bağlantı kurulurken bir hata oluştu.');
            });
        });
    });
});
</script>
@endpush
