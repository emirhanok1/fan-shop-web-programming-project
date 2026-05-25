@extends('layouts.admin')

@section('title', 'Sipariş Detayı #' . $order->id . ' - FanStore Admin')
@section('page_title', 'Sipariş Detayı #' . $order->id)

@section('content')
<div class="row">
    <!-- Order info, address, action buttons -->
    <div class="col-lg-4">
        <!-- Order Stats Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Sipariş Bilgileri</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-secondary fw-semibold">Fatura No:</td>
                        <td class="fw-bold"><code>{{ $order->invoice_no ?? '-' }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-secondary fw-semibold">Durum:</td>
                        <td>
                            @switch($order->status)
                                @case('pending')
                                    <span class="badge bg-warning text-dark">Beklemede</span>
                                    @break
                                @case('approved')
                                    <span class="badge bg-info text-white">Onaylandı</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger text-white">İptal Edildi</span>
                                    @break
                                @case('confirmed')
                                    <span class="badge bg-success text-white">Tamamlandı</span>
                                    @break
                            @endswitch
                        </td>
                    </tr>
                    <tr>
                        <td class="text-secondary fw-semibold">Tutar:</td>
                        <td class="fw-bold text-success">{{ number_format($order->total_amount, 2) }} TL</td>
                    </tr>
                    <tr>
                        <td class="text-secondary fw-semibold">Bakiye Kullanımı:</td>
                        <td>{{ number_format($order->balance_used, 2) }} TL</td>
                    </tr>
                    <tr>
                        <td class="text-secondary fw-semibold">Kart Ödemesi:</td>
                        <td>{{ number_format($order->card_amount, 2) }} TL</td>
                    </tr>
                    <tr>
                        <td class="text-secondary fw-semibold">Tarih:</td>
                        <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Teslimat Adresi</h5>
            </div>
            <div class="card-body">
                <p class="mb-0 text-dark">{{ $order->shipping_address }}</p>
            </div>
        </div>

        <!-- Admin Actions -->
        @if($order->status !== 'cancelled' && $order->status !== 'confirmed')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold">Sipariş İşlemleri</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    @if($order->status === 'pending')
                        <form action="{{ route('admin.orders.approve', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i>Siparişi Onayla</button>
                        </form>
                    @endif

                    @if($order->status === 'approved' && $order->tracking)
                        <form action="{{ route('admin.orders.advance', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-arrow-right me-2"></i>Aşamayı İlerlet ({{ $order->tracking->step }})
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.orders.reject', $order) }}" method="POST" onsubmit="return confirm('Bu siparişi iptal etmek istediğinize emin misiniz? Bakiye kullanımı iade edilecektir.');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100"><i class="fas fa-times me-2"></i>Siparişi İptal Et</button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- Items and Tracking step -->
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Sipariş İçeriği</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Ürün</th>
                                <th>Birim Fiyat</th>
                                <th>Adet</th>
                                <th class="pe-3 text-end">Toplam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->primary_image_path)
                                                <img src="{{ asset($item->product->primary_image_path) }}" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $item->product->name ?? 'Silinmiş Ürün' }}</div>
                                                <small class="text-muted">{{ $item->product->franchise ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($item->price, 2) }} TL</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="pe-3 text-end fw-semibold">{{ number_format($item->price * $item->quantity, 2) }} TL</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tracking Status -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Kargo Takip Durumu</h5>
            </div>
            <div class="card-body">
                @if($order->tracking)
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="fw-bold text-dark mb-1">Aşama: <span class="text-primary text-uppercase">{{ $order->tracking->step }}</span></div>
                        <div class="text-secondary small">{{ $order->tracking->description }}</div>
                        <div class="text-muted small mt-2">Son güncelleme: {{ $order->tracking->updated_at->format('d.m.Y H:i') }}</div>
                    </div>
                @else
                    <p class="text-muted mb-0">Bu sipariş için henüz takip kaydı oluşturulmamış (Siparişin onaylanması gerekir).</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
