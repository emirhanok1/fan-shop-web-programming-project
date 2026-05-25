@extends('layouts.app')

@section('title', 'Siparişlerim - FanStore')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 font-weight-bold"><i class="fas fa-box text-danger me-2"></i>Siparişlerim</h1>
            <p class="text-muted">Geçmiş siparişlerinizin durumunu inceleyebilir ve kargo takibi yapabilirsiniz.</p>
        </div>
    </div>

    <div class="fs-card p-4">
        @if($orders->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-receipt fa-3x text-muted opacity-50"></i>
                </div>
                <h5 class="fw-semibold">Henüz siparişiniz bulunmuyor.</h5>
                <p class="text-muted mb-4">Harika dizi ve film lisanslı ürünlerimizle koleksiyonunuzu oluşturmaya hemen başlayın.</p>
                <a href="{{ route('products.index') }}" class="btn btn-accent px-4">Alışverişe Başla</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle" style="color: var(--fs-text);">
                    <thead>
                        <tr class="text-muted border-bottom" style="border-color: var(--fs-border) !important;">
                            <th scope="col" class="pb-3">Sipariş No</th>
                            <th scope="col" class="pb-3">Tarih</th>
                            <th scope="col" class="pb-3">Ürünler</th>
                            <th scope="col" class="pb-3 text-end">Toplam Tutar</th>
                            <th scope="col" class="pb-3 text-center">Durum</th>
                            <th scope="col" class="pb-3 text-center" style="width: 100px;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="border-bottom" style="border-color: var(--fs-border) !important;">
                                <td class="py-3 fw-bold">
                                    <a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-danger">
                                        {{ $order->invoice_no ?? '#' . $order->id }}
                                    </a>
                                </td>
                                <td class="py-3 text-muted">
                                    {{ $order->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="py-3 small text-truncate" style="max-width: 250px;">
                                    @php
                                        $itemNames = $order->items->map(function($item) {
                                            return $item->product->name . ' (x' . $item->quantity . ')';
                                        })->join(', ');
                                    @endphp
                                    <span title="{{ $itemNames }}">{{ $itemNames }}</span>
                                </td>
                                <td class="py-3 text-end fw-bold">
                                    {{ number_format($order->total_amount, 2) }} ₺
                                </td>
                                <td class="py-3 text-center">
                                    @if($order->status === 'pending')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Onay Bekliyor</span>
                                    @elseif($order->status === 'approved')
                                        <span class="badge bg-info text-dark"><i class="fas fa-truck me-1"></i>Onaylandı</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>İptal Edildi</span>
                                    @elseif($order->status === 'confirmed')
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Teslim Alındı</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-accent-outline py-1">
                                        Detay
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
