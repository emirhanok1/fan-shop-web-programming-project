@extends('layouts.app')

@section('title', 'Sipariş Detayı - FanStore')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('orders.index') }}" class="text-muted small text-decoration-none d-inline-block mb-2">
                <i class="fas fa-chevron-left me-1"></i> Siparişlerime Dön
            </a>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="h2 font-weight-bold mb-1">
                        Sipariş: {{ $order->invoice_no ?? '#' . $order->id }}
                    </h1>
                    <p class="text-muted mb-0">Tarih: {{ $order->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <div class="mt-3 mt-md-0">
                    @if($order->status === 'pending')
                        <span class="badge bg-warning text-dark fs-6 py-2 px-3"><i class="fas fa-clock me-1"></i>Onay Bekliyor</span>
                    @elseif($order->status === 'approved')
                        <span class="badge bg-info text-dark fs-6 py-2 px-3"><i class="fas fa-truck me-1"></i>Onaylandı</span>
                    @elseif($order->status === 'cancelled')
                        <span class="badge bg-danger fs-6 py-2 px-3"><i class="fas fa-times-circle me-1"></i>İptal Edildi</span>
                    @elseif($order->status === 'confirmed')
                        <span class="badge bg-success fs-6 py-2 px-3"><i class="fas fa-check-circle me-1"></i>Teslim Alındı</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left: Order details & Tracking --}}
        <div class="col-lg-8">
            
            {{-- 1. Progress Bar (If Approved or Confirmed) --}}
            @if($order->status === 'approved' || $order->status === 'confirmed')
                @php
                    $steps = ['sourcing', 'packaging', 'shipped', 'on_the_way', 'delivered'];
                    $stepNames = [
                        'sourcing' => 'Tedarik Ediliyor',
                        'packaging' => 'Kutulanıyor',
                        'shipped' => 'Kargoya Verildi',
                        'on_the_way' => 'Yola Çıktı',
                        'delivered' => 'Teslim Edildi'
                    ];
                    $stepIcons = [
                        'sourcing' => 'fa-boxes',
                        'packaging' => 'fa-archive',
                        'shipped' => 'fa-shipping-fast',
                        'on_the_way' => 'fa-road',
                        'delivered' => 'fa-home'
                    ];
                    $currentStep = $order->tracking ? $order->tracking->step : null;
                    $stepIndex = $currentStep ? array_search($currentStep, $steps) : -1;
                    if ($order->status === 'confirmed') {
                        $stepIndex = 4; // Complete all
                    }
                    $progressPercent = $stepIndex >= 0 ? ($stepIndex / (count($steps) - 1)) * 100 : 0;
                @endphp
                
                <div class="fs-card p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-shipping-fast text-danger me-2"></i>Kargo Takip</h5>
                    <hr class="mb-5" style="border-color: var(--fs-border);">
                    
                    <div class="position-relative py-2 px-4 mb-5">
                        <div class="tracking-wrapper">
                            <div class="tracking-line"></div>
                            <div class="tracking-progress-line" style="width: {{ $progressPercent }}%;"></div>
                            
                            @foreach($steps as $idx => $step)
                                <div class="tracking-step-node {{ $idx < $stepIndex ? 'completed' : ($idx == $stepIndex ? 'active' : '') }}"
                                     title="{{ $stepNames[$step] }}">
                                    <i class="fas {{ $stepIcons[$step] }}"></i>
                                    <span class="tracking-step-label text-center">{{ $stepNames[$step] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- OpenWeatherMap Weather warning placeholder (Faz 7'de eklenecek) --}}
                    <div class="alert alert-info py-2 px-3 border-0 small d-flex align-items-center mb-0 mt-4" style="background: rgba(13, 110, 253, 0.08); color: #0d6efd;">
                        <i class="fas fa-cloud-sun-rain me-2 fa-lg"></i>
                        <div>
                            <strong>Hava Durumu Servisi (Faz 7 Entegrasyonu):</strong> Teslimat şehrinizdeki hava durumuna göre kargo uyarıları burada gösterilecektir.
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. Products List --}}
            <div class="fs-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-shopping-basket text-danger me-2"></i>Sipariş Verilen Ürünler</h5>
                <hr class="my-3" style="border-color: var(--fs-border);">
                
                <div class="table-responsive">
                    <table class="table align-middle" style="color: var(--fs-text);">
                        <thead>
                            <tr class="text-muted">
                                <th scope="col">Görsel</th>
                                <th scope="col">Ürün Adı</th>
                                <th scope="col" class="text-center">Adet</th>
                                <th scope="col" class="text-end">Birim Fiyat</th>
                                <th scope="col" class="text-end">Toplam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr class="border-bottom" style="border-color: var(--fs-border) !important;">
                                    <td style="width: 70px;">
                                        <img src="{{ asset($item->product->primary_image_path) }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="rounded border" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-color: var(--fs-border) !important;">
                                    </td>
                                    <td>
                                        <span class="fw-semibold d-block">{{ $item->product->name }}</span>
                                        @if($item->product->franchise)
                                            <span class="franchise-badge">{{ $item->product->franchise }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price, 2) }} ₺</td>
                                    <td class="text-end fw-bold">{{ number_format($item->quantity * $item->unit_price, 2) }} ₺</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-start gap-2">
                @if($order->status === 'pending')
                    <button type="button" class="btn btn-danger px-4" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                        <i class="fas fa-times-circle me-2"></i>Siparişi İptal Et
                    </button>
                @endif

                @if($order->status === 'approved' && $order->tracking && $order->tracking->step === 'delivered')
                    <form action="{{ route('orders.confirm', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 py-2 fw-bold">
                            <i class="fas fa-check-circle me-2"></i>Ürünlerimi Teslim Aldım
                        </button>
                    </form>
                @endif
            </div>

        </div>

        {{-- Right: Billing & Shipping Summary --}}
        <div class="col-lg-4">
            {{-- Billing summary --}}
            <div class="fs-card p-4 mb-4">
                <h5 class="fw-bold mb-3">Fatura & Ödeme Özeti</h5>
                <hr class="my-3" style="border-color: var(--fs-border);">
                
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Ara Toplam</span>
                    <span class="fw-semibold">{{ number_format($order->total_amount, 2) }} ₺</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Kargo</span>
                    <span class="text-success fw-semibold">Ücretsiz</span>
                </div>
                @if($order->balance_used > 0)
                    <div class="d-flex justify-content-between mb-2 small text-success">
                        <span>Cüzdandan Ödenen</span>
                        <span class="fw-semibold">-{{ number_format($order->balance_used, 2) }} ₺</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between mb-3 small">
                    <span class="text-muted">Karttan Çekilen</span>
                    <span class="fw-semibold">{{ number_format($order->card_amount, 2) }} ₺</span>
                </div>
                
                <hr class="my-2" style="border-color: var(--fs-border);">
                
                <div class="d-flex justify-content-between mb-0 pt-2">
                    <span class="fw-bold">Toplam Ödeme</span>
                    <span class="h5 mb-0 fw-extrabold text-danger">{{ number_format($order->total_amount, 2) }} ₺</span>
                </div>
            </div>

            {{-- Shipping address --}}
            <div class="fs-card p-4">
                <h5 class="fw-bold mb-3">Teslimat Adresi</h5>
                <hr class="my-3" style="border-color: var(--fs-border);">
                <p class="small text-muted mb-0" style="line-height: 1.6;">
                    <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $order->shipping_address }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Order Modal --}}
@if($order->status === 'pending')
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold" id="cancelOrderModalLabel">Sipariş İptal Onayı</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <p class="mb-3 fw-semibold">Siparişi iptal etmek istediğinize emin misiniz?</p>
                    <p class="small text-muted">
                        İptal işleminden sonra sipariş tutarı olan <strong>{{ number_format($order->total_amount, 2) }} ₺</strong> cüzdan bakiyenize hediye kupon olarak iade edilecektir. Kredi kartı iadesi yapılmamaktadır.
                    </p>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Vazgeç</button>
                    <form action="{{ route('orders.cancel', $order) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Siparişi İptal Et</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@push('styles')
<style>
.tracking-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}
.tracking-line {
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--fs-border);
    z-index: 1;
    transform: translateY(-50%);
}
.tracking-progress-line {
    position: absolute;
    top: 50%;
    left: 0;
    height: 4px;
    background: var(--fs-accent);
    z-index: 2;
    transform: translateY(-50%);
    transition: width 0.4s ease;
}
.tracking-step-node {
    position: relative;
    z-index: 3;
    background: var(--fs-card);
    border: 3px solid var(--fs-border);
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: var(--fs-text-muted);
    transition: all 0.3s ease;
}
.tracking-step-node.active {
    border-color: var(--fs-accent);
    color: var(--fs-accent);
    box-shadow: 0 0 12px rgba(229, 9, 20, 0.4);
}
.tracking-step-node.completed {
    background: var(--fs-accent);
    border-color: var(--fs-accent);
    color: #fff;
}
.tracking-step-label {
    position: absolute;
    top: 52px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    font-size: 0.68rem;
    font-weight: 600;
    color: var(--fs-text);
}
</style>
@endpush
