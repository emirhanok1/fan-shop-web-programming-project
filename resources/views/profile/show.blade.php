@extends('layouts.app')

@section('title', 'Profilim - FanStore')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 font-weight-bold"><i class="fas fa-user-cog text-danger me-2"></i>Profil ve Hesap Ayarları</h1>
            <p class="text-muted">Profil bilgilerinizi düzenleyebilir, cüzdan geçmişinizi görüntüleyebilir ve hesap güvenliğinizi yönetebilirsiniz.</p>
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
        {{-- Left: Tab Navigation --}}
        <div class="col-lg-3">
            <div class="fs-card p-3">
                <div class="nav flex-column nav-pills" id="profileTabs" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active text-start mb-2 py-2.5 fw-semibold d-flex align-items-center" id="basic-tab" data-bs-toggle="pill" data-bs-target="#basic" type="button" role="tab" aria-controls="basic" aria-selected="true">
                        <i class="fas fa-id-card me-2 text-danger"></i>Profil Bilgileri
                    </button>
                    <button class="nav-link text-start mb-2 py-2.5 fw-semibold d-flex align-items-center" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                        <i class="fas fa-key me-2 text-danger"></i>Şifre Değiştir
                    </button>
                    <button class="nav-link text-start mb-2 py-2.5 fw-semibold d-flex align-items-center" id="wallet-tab" data-bs-toggle="pill" data-bs-target="#wallet" type="button" role="tab" aria-controls="wallet" aria-selected="false">
                        <i class="fas fa-wallet me-2 text-danger"></i>Cüzdan & İşlemler
                    </button>
                    <button class="nav-link text-start py-2.5 fw-semibold d-flex align-items-center text-danger" id="deactivate-tab" data-bs-toggle="pill" data-bs-target="#deactivate" type="button" role="tab" aria-controls="deactivate" aria-selected="false">
                        <i class="fas fa-user-slash me-2"></i>Hesabı Pasifleştir
                    </button>
                </div>
            </div>
        </div>

        {{-- Right: Tab Content --}}
        <div class="col-lg-9">
            <div class="tab-content" id="profileTabsContent">
                
                {{-- Tab 1: Profil Bilgileri --}}
                <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                    
                    {{-- Avatar Section --}}
                    <div class="fs-card p-4 mb-4">
                        <h5 class="fw-bold mb-3">Profil Resmi</h5>
                        <hr class="my-3" style="border-color: var(--fs-border);">
                        
                        <div class="d-flex flex-column flex-sm-row align-items-center gap-4">
                            <div>
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover; border-color: var(--fs-border) !important;">
                                @else
                                    <div class="rounded-circle border d-flex align-items-center justify-content-center bg-dark text-muted" style="width: 100px; height: 100px; border-color: var(--fs-border) !important;">
                                        <i class="fas fa-user fa-3x"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1 text-center text-sm-start">
                                <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <input class="form-control form-control-sm bg-transparent border-secondary text-reset fs-search-input d-inline-block w-auto" 
                                               type="file" name="avatar" id="avatar" required accept="image/*">
                                    </div>
                                    <button type="submit" class="btn btn-accent btn-sm">Resmi Güncelle</button>
                                </form>
                                <small class="text-muted d-block mt-2">JPEG, JPG, PNG veya WEBP formatında, maksimum 2 MB boyutunda görseller desteklenir.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Form Details --}}
                    <div class="fs-card p-4">
                        <h5 class="fw-bold mb-3">Kişisel Bilgiler</h5>
                        <hr class="my-3" style="border-color: var(--fs-border);">

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label small fw-semibold">Ad Soyad</label>
                                    <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label small fw-semibold">E-posta Adresi</label>
                                    <input type="email" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="phone" class="form-label small fw-semibold">Telefon Numarası</label>
                                    <input type="text" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+90 5xx xxx xx xx">
                                </div>
                            </div>
                            <div class="mt-4 pt-2">
                                <button type="submit" class="btn btn-accent px-4">Bilgileri Kaydet</button>
                            </div>
                        </form>
                    </div>

                </div>

                {{-- Tab 2: Şifre Değiştir --}}
                <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                    <div class="fs-card p-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-shield-alt text-danger me-2"></i>Şifre Güncelleme</h5>
                        <hr class="my-3" style="border-color: var(--fs-border);">

                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="current_password" class="form-label small fw-semibold">Mevcut Şifre</label>
                                    <input type="password" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                           id="current_password" name="current_password" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label small fw-semibold">Yeni Şifre</label>
                                    <input type="password" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                           id="password" name="password" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label small fw-semibold">Yeni Şifre Tekrarı</label>
                                    <input type="password" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                            <div class="mt-4 pt-2">
                                <button type="submit" class="btn btn-accent px-4">Şifreyi Değiştir</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Tab 3: Bakiye & İşlemler --}}
                <div class="tab-pane fade" id="wallet" role="tabpanel" aria-labelledby="wallet-tab">
                    <div class="fs-card p-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-wallet text-danger me-2"></i>Cüzdan Bakiyem</h5>
                        <hr class="my-3" style="border-color: var(--fs-border);">
                        
                        <div class="row align-items-center mb-4">
                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Mevcut Bakiyeniz</p>
                                <h3 class="display-5 fw-extrabold text-success mb-0">
                                    {{ number_format($user->balance, 2) }} ₺
                                </h3>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <span class="badge bg-success py-2 px-3 fs-6">
                                    <i class="fas fa-gift me-2"></i>Hediye İade Bakiyesi
                                </span>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 mt-5">Son 10 Finansal İşlem</h6>
                        <div class="table-responsive">
                            <table class="table align-middle" style="color: var(--fs-text);">
                                <thead>
                                    <tr class="text-muted border-bottom" style="border-color: var(--fs-border) !important;">
                                        <th scope="col" class="pb-2">Tarih</th>
                                        <th scope="col" class="pb-2">İşlem Türü</th>
                                        <th scope="col" class="pb-2">Açıklama</th>
                                        <th scope="col" class="pb-2 text-end">Tutar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($transactions->isEmpty())
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted small">
                                                Henüz finansal bir işlem hareketiniz bulunmamaktadır.
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($transactions as $tx)
                                            <tr class="border-bottom" style="border-color: var(--fs-border) !important;">
                                                <td class="py-3 small text-muted">
                                                    {{ $tx->created_at->format('d.m.Y H:i') }}
                                                </td>
                                                <td class="py-3">
                                                    @if($tx->type === 'payment')
                                                        <span class="badge bg-warning text-dark"><i class="fas fa-minus me-1"></i>Ödeme</span>
                                                    @elseif($tx->type === 'refund')
                                                        <span class="badge bg-success"><i class="fas fa-plus me-1"></i>İade</span>
                                                    @elseif($tx->type === 'bonus')
                                                        <span class="badge bg-primary"><i class="fas fa-gift me-1"></i>Hediye</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $tx->type }}</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 small">
                                                    {{ $tx->description }}
                                                </td>
                                                <td class="py-3 text-end fw-bold {{ $tx->amount < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ $tx->amount < 0 ? '' : '+' }}{{ number_format($tx->amount, 2) }} ₺
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Tab 4: Hesabı Pasifleştir --}}
                <div class="tab-pane fade" id="deactivate" role="tabpanel" aria-labelledby="deactivate-tab">
                    <div class="fs-card p-4 border-danger" style="border-color: rgba(229, 9, 20, 0.4) !important;">
                        <h5 class="fw-bold text-danger mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Hesabı Pasif Hale Getir</h5>
                        <hr class="my-3" style="border-color: var(--fs-border);">
                        
                        <p class="small mb-4 text-muted" style="line-height: 1.6;">
                            Hesabınızı pasif hale getirdiğinizde sisteme tekrar giriş yapamazsınız, mevcut sepetiniz temizlenir ve üyeliğiniz askıya alınır. Siparişleriniz ve cüzdan bakiyeniz sistemde saklı tutulacaktır. 
                            Hesabınızı tekrar aktifleştirmek için yöneticiyle iletişime geçmeniz gerekecektir.
                        </p>

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal">
                            <i class="fas fa-user-slash me-2"></i>Hesabımı Pasifleştir
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Deactivate Confirmation Modal --}}
<div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <form action="{{ route('profile.deactivate') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold" id="deactivateModalLabel">Hesap Pasifleştirme Onayı</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-slash fa-3x text-danger"></i>
                    </div>
                    <p class="text-center fw-semibold mb-3">Hesabınızı askıya almak istediğinizden emin misiniz?</p>
                    <p class="small text-muted text-center mb-4">
                        Bu işlem oturumunuzu kapatacak ve hesabınızı pasif konuma getirecektir. Devam etmek için lütfen mevcut şifrenizi girerek onaylayın:
                    </p>
                    <div class="mb-0">
                        <label for="password" class="form-label small fw-semibold">Mevcut Şifreniz</label>
                        <input type="password" class="form-control bg-transparent border-secondary text-reset fs-search-input" 
                               id="deactivate_password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Onayla ve Hesabı Kapat</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
#profileTabs .nav-link {
    color: var(--fs-text) !important;
    background: transparent;
    transition: all 0.2s ease;
    border-radius: var(--fs-radius);
}
#profileTabs .nav-link:hover {
    background: rgba(229, 9, 20, 0.05);
    color: var(--fs-accent) !important;
}
#profileTabs .nav-link.active {
    background: var(--fs-accent) !important;
    color: #fff !important;
}
#profileTabs .nav-link.active i {
    color: #fff !important;
}
</style>
@endpush
