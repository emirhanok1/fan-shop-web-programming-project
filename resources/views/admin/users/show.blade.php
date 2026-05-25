@extends('layouts.admin')

@section('title', 'Kullanıcı Detayı - FanStore Admin')
@section('page_title', 'Kullanıcı Detayı')

@section('content')
<div class="row">
    <!-- User Info and Edit Form -->
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    @if($user->avatar)
                        <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle shadow-sm fw-bold" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted small mb-3">{{ $user->email }}</p>
                <div class="d-flex justify-content-center gap-2">
                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                        {{ $user->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                    <span class="badge bg-primary px-3 py-2 text-capitalize">
                        {{ $user->role }}
                    </span>
                </div>
            </div>
            <div class="card-footer bg-white border-0 px-4 pb-4">
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <div class="small text-muted mb-0.5">Mevcut Bakiye</div>
                        <div class="h5 fw-bold text-success mb-0">{{ number_format($user->balance, 2) }} TL</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted mb-0.5">Sipariş Sayısı</div>
                        <div class="h5 fw-bold text-dark mb-0">{{ $user->orders->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-user-edit me-1 text-secondary"></i> Bilgileri Güncelle</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-3 small">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold text-secondary">Ad Soyad</label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold text-secondary">E-posta Adresi</label>
                        <input type="email" name="email" id="email" class="form-control form-control-sm" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Geri Dön</a>
                        <button type="submit" class="btn btn-sm btn-primary px-3">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Orders and Transactions -->
    <div class="col-lg-8">
        <!-- Orders History -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-shopping-bag me-1 text-primary"></i> Sipariş Geçmişi</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-3">Sipariş No</th>
                                <th>Tarih</th>
                                <th>Tutar</th>
                                <th>Durum</th>
                                <th class="pe-3 text-end">Detay</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->orders as $order)
                                <tr>
                                    <td class="ps-3 fw-bold">#{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
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
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-light">
                                            İncele
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Bu kullanıcıya ait sipariş bulunamadı.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Transactions Ledger -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-wallet me-1 text-success"></i> Hesap Hareketleri (Cüzdan)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>Tarih</th>
                                <th>Tür</th>
                                <th>Tutar</th>
                                <th>Açıklama</th>
                                <th class="pe-3">Sipariş No</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->transactions as $transaction)
                                <tr>
                                    <td class="ps-3 text-muted">#{{ $transaction->id }}</td>
                                    <td>{{ $transaction->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        @switch($transaction->type)
                                            @case('deposit')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Yükleme</span>
                                                @break
                                            @case('payment')
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Ödeme</span>
                                                @break
                                            @case('refund')
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">İade</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="fw-bold @if($transaction->type == 'payment') text-danger @else text-success @endif">
                                        @if($transaction->type == 'payment') - @else + @endif{{ number_format($transaction->amount, 2) }} TL
                                    </td>
                                    <td>{{ $transaction->description }}</td>
                                    <td class="pe-3">
                                        @if($transaction->order_id)
                                            <a href="{{ route('admin.orders.show', $transaction->order_id) }}" class="text-decoration-none fw-bold">
                                                #{{ $transaction->order_id }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Herhangi bir hesap hareketi bulunamadı.</td>
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
