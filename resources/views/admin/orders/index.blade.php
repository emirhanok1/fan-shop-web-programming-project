@extends('layouts.admin')

@section('title', 'Sipariş Yönetimi - FanStore Admin')
@section('page_title', 'Sipariş Yönetimi')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Sipariş No</th>
                        <th>Fatura No</th>
                        <th>Müşteri</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                        <th class="pe-3 text-end" style="width: 150px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="ps-3 fw-bold">#{{ $order->id }}</td>
                            <td><code>{{ $order->invoice_no ?? '-' }}</code></td>
                            <td>{{ $order->user->name ?? 'Bilinmeyen Müşteri' }}</td>
                            <td class="fw-semibold">{{ number_format($order->total_amount, 2) }} TL</td>
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
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="Detay">
                                    <i class="fas fa-eye"></i> İncele
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Henüz sipariş bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
