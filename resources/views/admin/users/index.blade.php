@extends('layouts.admin')

@section('title', 'Kullanıcı Yönetimi - FanStore Admin')
@section('page_title', 'Kullanıcı Yönetimi')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 fw-bold">Kullanıcı Filtrele</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
            <div class="col-md-9">
                <input type="text" name="q" id="q" class="form-control" placeholder="Ad Soyad veya E-posta ile ara..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-secondary w-100 me-2">Filtrele</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Temizle</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Ad Soyad</th>
                        <th>E-posta</th>
                        <th>Bakiye</th>
                        <th>Durum</th>
                        <th>Kayıt Tarihi</th>
                        <th class="pe-3 text-end" style="width: 150px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold">{{ $user->name }}</div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td class="fw-semibold text-success">{{ number_format($user->balance, 2) }} TL</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input status-toggle" type="checkbox" data-id="{{ $user->id }}" id="switch-{{ $user->id }}" {{ $user->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary me-1" title="Detay / Düzenle">
                                    <i class="fas fa-eye"></i> İncele
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Kriterlere uygun kullanıcı bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Toplam <b>{{ $users->total() }}</b> kullanıcıdan <b>{{ $users->firstItem() }}-{{ $users->lastItem() }}</b> arası gösteriliyor.
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.status-toggle').on('change', function() {
            let userId = $(this).data('id');
            let isChecked = $(this).is(':checked');
            
            $.ajax({
                url: `/admin/users/${userId}/toggle`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // successfully toggled
                    } else {
                        alert('Durum güncellenemedi.');
                        $(`#switch-${userId}`).prop('checked', !isChecked);
                    }
                },
                error: function() {
                    alert('Bir bağlantı hatası oluştu.');
                    $(`#switch-${userId}`).prop('checked', !isChecked);
                }
            });
        });
    });
</script>
@endpush
