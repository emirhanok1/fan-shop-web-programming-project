@extends('layouts.admin')

@section('title', 'Yeni Ürün Ekle - FanStore Admin')
@section('page_title', 'Yeni Ürün Ekle')

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

                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-secondary">Ürün Adı <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label fw-semibold text-secondary">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Seçiniz</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="franchise" class="form-label fw-semibold text-secondary">Franchise (Dizi/Film) <span class="text-danger">*</span></label>
                            <input type="text" name="franchise" id="franchise" class="form-control" value="{{ old('franchise') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-semibold text-secondary">Fiyat (TL) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price') }}" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label fw-semibold text-secondary">Stok Adedi <span class="text-danger">*</span></label>
                            <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock') }}" required min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold text-secondary">Açıklama</label>
                        <textarea name="description" id="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                    </div>

                    <hr class="my-4 text-muted">

                    <h5 class="fw-bold mb-3">Görseller</h5>
                    <div class="mb-3">
                        <label for="images" class="form-label fw-semibold text-secondary">Görsel Seç (En fazla 5 adet, jpeg, jpg, png, webp. Maks 2MB)</label>
                        <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                        <small class="text-muted mt-1 d-block">İlk yüklenen görsel otomatik olarak ana görsel (primary) olarak ayarlanacaktır.</small>
                    </div>

                    <hr class="my-4 text-muted">

                    <h5 class="fw-bold mb-3">Yayın Seçenekleri</h5>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-secondary" for="is_active">Satışa Açık / Aktif</label>
                        </div>
                    </div>

                    <!-- Hidden TMDB fields -->
                    <input type="hidden" name="tmdb_id" id="tmdb_id" value="{{ old('tmdb_id') }}">
                    <input type="hidden" name="tmdb_type" id="tmdb_type" value="{{ old('tmdb_type', 'movie') }}">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Geri Dön</a>
                        <button type="submit" class="btn btn-primary px-4">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TMDB Sidebar Assistant -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-search me-1 text-primary"></i> TMDB Veritabanı Asistanı</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">Bu asistan sayesinde franchise dizi/film bilgilerini aratarak ürünün TMDB ID ve TMDB Türü alanlarını otomatik doldurabilirsiniz.</p>
                
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
                        let posterUrl = item.poster ? item.poster : 'https://placehold.co/92x138?text=Görsel+Yok';
                        let year = item.year ? `(${item.year})` : '';
                        
                        let itemHtml = `
                            <button type="button" class="list-group-item list-group-item-action d-flex align-items-center py-2 select-tmdb-item" 
                                    data-id="${item.id}" 
                                    data-type="${item.media_type}"
                                    data-title="${item.title}">
                                <img src="${posterUrl}" class="rounded me-3" style="width: 40px; height: 60px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold small text-dark">${item.title} ${year}</div>
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

            // visually notify user
            alert(`TMDB Bilgileri Seçildi:\nBaşlık: ${title}\nID: ${id}\nTür: ${type}`);
            
            // Highlight selected item
            $('.select-tmdb-item').removeClass('active');
            $(this).addClass('active');
        });
    });
</script>
@endpush
