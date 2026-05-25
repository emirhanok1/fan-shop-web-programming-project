@extends('layouts.app')

@section('title', 'Panelim - FanStore')

@section('content')
<div class="container py-4">
    {{-- Welcome --}}
    <div class="mb-4">
        <h2 class="fw-bold">Hoş geldin, <span style="color: var(--fs-accent);">{{ $user->name }}</span> 👋</h2>
        <p style="color: var(--fs-text-muted);">Hesap özetini buradan takip edebilirsin.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="fs-card p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(40,167,69,0.12);">
                        <i class="fas fa-wallet fa-lg text-success"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold" style="color: var(--fs-text-muted);">Bakiye</div>
                        <div class="h4 fw-bold mb-0 text-success">{{ number_format($user->balance, 2) }} ₺</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fs-card p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(229,9,20,0.12);">
                        <i class="fas fa-shopping-bag fa-lg" style="color: var(--fs-accent);"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold" style="color: var(--fs-text-muted);">Toplam Sipariş</div>
                        <div class="h4 fw-bold mb-0">{{ $totalOrders }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fs-card p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(13,110,253,0.12);">
                        <i class="fas fa-user-circle fa-lg text-primary"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold" style="color: var(--fs-text-muted);">Hesap Durumu</div>
                        <div class="h5 fw-bold mb-0">
                            @if($user->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Pasif</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('products.index') }}" class="text-decoration-none">
                <div class="fs-card p-3 text-center" style="cursor: pointer;">
                    <i class="fas fa-shopping-cart fa-lg mb-2" style="color: var(--fs-accent);"></i>
                    <div class="small fw-semibold">Alışverişe Devam</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('orders.index') }}" class="text-decoration-none">
                <div class="fs-card p-3 text-center" style="cursor: pointer;">
                    <i class="fas fa-box fa-lg mb-2" style="color: var(--fs-accent);"></i>
                    <div class="small fw-semibold">Siparişlerim</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('profile.show') }}" class="text-decoration-none">
                <div class="fs-card p-3 text-center" style="cursor: pointer;">
                    <i class="fas fa-user fa-lg mb-2" style="color: var(--fs-accent);"></i>
                    <div class="small fw-semibold">Profilim</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('addresses.index') }}" class="text-decoration-none">
                <div class="fs-card p-3 text-center" style="cursor: pointer;">
                    <i class="fas fa-map-marker-alt fa-lg mb-2" style="color: var(--fs-accent);"></i>
                    <div class="small fw-semibold">Adreslerim</div>
                </div>
            </a>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="fs-card">
        <div class="p-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--fs-border);">
            <h5 class="fw-bold mb-0"><i class="fas fa-history me-2" style="color: var(--fs-accent);"></i>Son Siparişler</h5>
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-accent-outline">Tümünü Gör</a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="color: var(--fs-text);">
                <thead>
                    <tr style="border-color: var(--fs-border);">
                        <th class="ps-3 small fw-semibold" style="color: var(--fs-text-muted);">Sipariş No</th>
                        <th class="small fw-semibold" style="color: var(--fs-text-muted);">Tarih</th>
                        <th class="small fw-semibold" style="color: var(--fs-text-muted);">Tutar</th>
                        <th class="small fw-semibold" style="color: var(--fs-text-muted);">Durum</th>
                        <th class="pe-3 text-end small fw-semibold" style="color: var(--fs-text-muted);">Detay</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr style="border-color: var(--fs-border);">
                            <td class="ps-3 fw-bold">#{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('d.m.Y') }}</td>
                            <td class="fw-semibold">{{ number_format($order->total_amount, 2) }} ₺</td>
                            <td>
                                @switch($order->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Beklemede</span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-info text-white">Onaylandı</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">İptal</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge bg-success">Tamamlandı</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-accent-outline">İncele</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color: var(--fs-text-muted);">
                                <i class="fas fa-box-open fa-2x mb-2 d-block" style="opacity: 0.3;"></i>
                                Henüz siparişiniz bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
