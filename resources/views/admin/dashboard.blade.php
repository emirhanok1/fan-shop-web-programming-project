@extends('layouts.admin')

@section('title', 'Dashboard - FanStore Admin')
@section('page_title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Statistic Cards -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info shadow-sm border-0">
            <div class="inner">
                <h3>{{ $totalUsers }}</h3>
                <p>Kullanıcılar</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                Detaylar <i class="fas fa-arrow-circle-right ms-1"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success shadow-sm border-0">
            <div class="inner">
                <h3>{{ $totalOrders }}</h3>
                <p>Toplam Sipariş</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="small-box-footer">
                Detaylar <i class="fas fa-arrow-circle-right ms-1"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning shadow-sm border-0">
            <div class="inner">
                <h3>{{ $pendingOrders }}</h3>
                <p>Bekleyen Sipariş</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="small-box-footer text-dark">
                Detaylar <i class="fas fa-arrow-circle-right ms-1"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger shadow-sm border-0">
            <div class="inner">
                <h3>{{ number_format($totalRevenue, 2) }} <small class="text-white-50" style="font-size: 1.2rem;">TL</small></h3>
                <p>Toplam Gelir</p>
            </div>
            <div class="icon">
                <i class="fas fa-lira-sign"></i>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="small-box-footer">
                Detaylar <i class="fas fa-arrow-circle-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Low Stock Alert Table -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                <h5 class="card-title mb-0 fw-bold">Stok Uyarısı (5'ten az)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Ürün Adı</th>
                                <th>Franchise</th>
                                <th>Stok</th>
                                <th class="pe-3 text-end">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $product)
                                <tr>
                                    <td class="ps-3 fw-semibold">{{ $product->name }}</td>
                                    <td>{{ $product->franchise }}</td>
                                    <td>
                                        <span class="badge bg-danger px-2.5 py-1.5">{{ $product->stock }}</span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                            Stok Düzenle
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Kritik stok seviyesinde ürün bulunmamaktadır.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center">
                <i class="fas fa-receipt text-primary me-2"></i>
                <h5 class="card-title mb-0 fw-bold">Son 5 Sipariş</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Sipariş No</th>
                                <th>Müşteri</th>
                                <th>Tutar</th>
                                <th>Durum</th>
                                <th class="pe-3 text-end">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td class="ps-3 fw-bold">#{{ $order->id }}</td>
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
                                    <td class="pe-3 text-end">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                                            İncele
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Henüz sipariş bulunmamaktadır.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
