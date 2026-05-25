@extends('layouts.admin')

@section('title', 'Ürünü Düzenle - FanStore Admin')
@section('page_title', 'Ürünü Düzenle')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Ürün Bilgileri</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-secondary">Ürün Adı <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label fw-semibold text-secondary">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Seçiniz</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="franchise" class="form-label fw-semibold text-secondary">Franchise (Dizi/Film) <span class="text-danger">*</span></label>
                            <input type="text" name="franchise" id="franchise" class="form-control" value="{{ old('franchise', $product->franchise) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-semibold text-secondary">Fiyat (TL) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label fw-semibold text-secondary">Stok Adedi <span class="text-danger">*</span></label>
                            <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold text-secondary">Açıklama</label>
                        <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <hr class="my-4 text-muted">

                    <h5 class="fw-bold mb-3">Yeni Görseller Ekle</h5>
                    <div class="mb-3">
                        <label for="images" class="form-label fw-semibold text-secondary">Görsel Seç (jpeg, jpg, png, webp. Maks 2MB)</label>
                        <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                        <small class="text-muted mt-1 d-block">Maksimum 5 görsel sınırına dikkat ediniz.</small>
                    </div>

                    <hr class="my-4 text-muted">

                    <h5 class="fw-bold mb-3">Yayın Seçenekleri</h5>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-secondary" for="is_active">Satışa Açık / Aktif</label>
                        </div>
                    </div>

                    <!-- Hidden TMDB fields -->
                    <input type="hidden" name="tmdb_id" id="tmdb_id" value="{{ old('tmdb_id', $product->tmdb_id) }}">
                    <input type="hidden" name="tmdb_type" id="tmdb_type" value="{{ old('tmdb_type', $product->tmdb_type) }}">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Geri Dön</a>
                        <button type="submit" class="btn btn-primary px-4">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Existing Images Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Mevcut Görseller</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($product->productImages as $image)
                        <div class="col-md-3 col-sm-4 col-6 mb-3" id="image-card-{{ $image->id }}">
                            <div class="card border shadow-sm h-100">
                                <img src="{{ asset($image->image_path) }}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                <div class="card-body p-2 d-flex justify-content-between align-items-center">
                                    <span class="badge {{ $image->is_primary ? 'bg-primary' : 'bg-secondary' }}" style="font-size: 0.75rem;">
                                        {{ $image->is_primary ? 'Ana Görsel' : 'Ek Görsel' }}
                                    </span>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-image-btn" data-id="{{ $image->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-3 text-muted">
                            Bu ürüne ait yüklenmiş herhangi bir görsel bulunmamaktadır.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- TMDB Assistant Sidebar -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-search me-1 text-primary"></i> TMDB Veritabanı Asistanı</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">Bu asistan sayesinde dizi/film bilgilerini aratarak ürünün TMDB ID ve Tür alanlarını güncelleyebilirsiniz.</p>
                
                <div class="input-group mb-3">
                    <input type="text" id="tmdb-search-input" class="form-control" placeholder="Dizi veya Film adı yazın...">
                    <button class="btn btn-primary" type="button" id="tmdb-search-btn">Ara</button>
                </div>

                <div id="tmdb-search-results" class="list-group" style="display: none; max-height: 350px; overflow-y: auto;">
                    <!-- AJAX results will be populated here -->
                </div>
                
                <div id="tmdb-assistant-feedback" class="small text-muted mt-2" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tmdb-search-btn').on('click', function() {
            searchTmdb();
        });

        $('#tmdb-search-input').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                searchTmdb();
            }
        });

        function searchTmdb() {
            let query = $('#tmdb-search-input').val().trim();
            if (!query) {
                alert('Lütfen arama terimi girin.');
                return;
            }

            $('#tmdb-search-results').hide().empty();
            $('#tmdb-assistant-feedback').show().text('Aranıyor...');

            $.ajax({
                url: '{{ route('admin.tmdb.search') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    q: query
                },
                success: function(results) {
                    $('#tmdb-assistant-feedback').hide();
                    if (results.length === 0) {
                        $('#tmdb-assistant-feedback').show().text('Sonuç bulunamadı (API anahtarınızı kontrol edin).');
                        return;
                    }

                    results.forEach(function(item) {
                        let mediaTypeLabel = item.media_type === 'tv' ? 'Dizi (TV)' : 'Film';
                        let posterUrl = item.poster_path ? item.poster_path : 'https://placehold.co/92x138?text=Görsel+Yok';
                        
                        let itemHtml = `
                            <button type="button" class="list-group-item list-group-item-action d-flex align-items-center py-2 select-tmdb-item" 
                                    data-id="${item.id}" 
                                    data-type="${item.media_type}"
                                    data-title="${item.title}">
                                <img src="${posterUrl}" class="rounded me-3" style="width: 40px; height: 60px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold small text-dark">${item.title}</div>
                                    <span class="badge bg-secondary p-1" style="font-size: 0.7rem;">${mediaTypeLabel}</span>
                                </div>
                            </button>
                        `;
                        $('#tmdb-search-results').append(itemHtml);
                    });
                    $('#tmdb-search-results').show();
                },
                error: function() {
                    $('#tmdb-assistant-feedback').show().text('TMDB ile bağlantı kurulurken bir hata oluştu.');
                }
            });
        }

        $(document).on('click', '.select-tmdb-item', function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let title = $(this).data('title');

            $('#tmdb_id').val(id);
            $('#tmdb_type').val(type);
            $('#franchise').val(title);

            alert(`TMDB Bilgileri Seçildi:\nBaşlık: ${title}\nID: ${id}\nTür: ${type}`);
            
            $('.select-tmdb-item').removeClass('active');
            $(this).addClass('active');
        });

        // Delete Image AJAX
        $('.delete-image-btn').on('click', function() {
            if (!confirm('Bu görseli silmek istediğinize emin misiniz?')) {
                return;
            }

            let imageId = $(this).data('id');
            let button = $(this);

            $.ajax({
                url: `/admin/products/images/${imageId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $(`#image-card-${imageId}`).fadeOut(300, function() {
                            $(this).remove();
                            // If no images left, display placeholder text
                            if ($('[id^="image-card-"]').length === 0) {
                                $('.card-body .row').append('<div class="col-12 text-center py-3 text-muted">Bu ürüne ait yüklenmiş herhangi bir görsel bulunmamaktadır.</div>');
                            }
                        });
                    } else {
                        alert('Görsel silinemedi.');
                    }
                },
                error: function() {
                    alert('Görsel silinirken bir bağlantı hatası oluştu.');
                }
            });
        });
    });
</script>
@endpush
