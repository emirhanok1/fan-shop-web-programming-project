@extends('layouts.admin')

@section('title', 'Ürün Yönetimi - FanStore Admin')
@section('page_title', 'Ürün Yönetimi')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">Ürün Filtrele</h5>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Yeni Ürün Ekle
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="q" class="form-label small fw-semibold text-secondary">Ürün Adı</label>
                <input type="text" name="q" id="q" class="form-control" placeholder="Ara..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <label for="category_id" class="form-label small fw-semibold text-secondary">Kategori</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">Tümü</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="franchise" class="form-label small fw-semibold text-secondary">Franchise (Dizi/Film)</label>
                <input type="text" name="franchise" id="franchise" class="form-control" placeholder="Örn: Breaking Bad" value="{{ request('franchise') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100 me-2">Filtrele</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Temizle</a>
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
                        <th class="ps-3" style="width: 80px;">Görsel</th>
                        <th>Ürün Adı</th>
                        <th>Kategori</th>
                        <th>Franchise</th>
                        <th>Fiyat</th>
                        <th>Stok</th>
                        <th>Satışta</th>
                        <th class="pe-3 text-end" style="width: 150px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="ps-3">
                                <img src="{{ asset($product->primary_image_path) }}" alt="{{ $product->name }}" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold">{{ $product->name }}</div>
                                <small class="text-muted">Slug: {{ $product->slug }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $product->category->name }}</span>
                            </td>
                            <td>{{ $product->franchise }}</td>
                            <td class="fw-bold">{{ number_format($product->price, 2) }} TL</td>
                            <td>
                                @if($product->stock < 5)
                                    <span class="badge bg-danger">{{ $product->stock }} (Kritik)</span>
                                @else
                                    <span class="badge bg-success">{{ $product->stock }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input status-toggle" type="checkbox" data-id="{{ $product->id }}" id="switch-{{ $product->id }}" {{ $product->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary me-1" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?');">
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
                            <td colspan="8" class="text-center text-muted py-5">Kriterlere uygun ürün bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Toplam <b>{{ $products->total() }}</b> üründen <b>{{ $products->firstItem() }}-{{ $products->lastItem() }}</b> arası gösteriliyor.
                </div>
                <div>
                    {{ $products->links() }}
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
            let productId = $(this).data('id');
            let isChecked = $(this).is(':checked');
            
            $.ajax({
                url: `/admin/products/${productId}/toggle`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Successfully toggled status
                    } else {
                        alert('Durum güncellenemedi.');
                        // revert checkbox status
                        $(`#switch-${productId}`).prop('checked', !isChecked);
                    }
                },
                error: function() {
                    alert('Bir bağlantı hatası oluştu.');
                    $(`#switch-${productId}`).prop('checked', !isChecked);
                }
            });
        });
    });
</script>
@endpush
