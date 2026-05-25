@extends('layouts.app')

@section('title', 'Sepetim - FanStore')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 font-weight-bold"><i class="fas fa-shopping-cart text-danger me-2"></i>Alışveriş Sepetim</h1>
            <p class="text-muted">Sepetinizdeki ürünleri düzenleyebilir ve alışverişinizi tamamlayabilirsiniz.</p>
        </div>
    </div>

    <div id="cartContent">
        @if($items->isEmpty())
            <div class="text-center py-5 fs-card p-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-basket fa-4x text-muted opacity-50"></i>
                </div>
                <h3 class="h5">Sepetinizde ürün bulunmamaktadır.</h3>
                <p class="text-muted mb-4">Dizi ve film dünyasının en havalı lisanslı ürünlerini sepetinize ekleyerek başlayın!</p>
                <a href="{{ route('products.index') }}" class="btn btn-accent px-4 py-2">
                    <i class="fas fa-arrow-left me-2"></i>Alışverişe Başla
                </a>
            </div>
        @else
            <div class="row g-4">
                {{-- Left: Cart Items --}}
                <div class="col-lg-8">
                    <div class="fs-card p-4 mb-4">
                        <div class="table-responsive">
                            <table class="table align-middle" style="color: var(--fs-text);">
                                <thead>
                                    <tr class="text-muted border-bottom" style="border-color: var(--fs-border) !important;">
                                        <th scope="col" colspan="2" class="pb-3">Ürün</th>
                                        <th scope="col" class="pb-3 text-center" style="width: 100px;">Fiyat</th>
                                        <th scope="col" class="pb-3 text-center" style="width: 140px;">Adet</th>
                                        <th scope="col" class="pb-3 text-end" style="width: 120px;">Ara Toplam</th>
                                        <th scope="col" class="pb-3 text-center" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr id="cart-item-{{ $item->id }}" class="border-bottom" style="border-color: var(--fs-border) !important;">
                                            {{-- Product Image --}}
                                            <td style="width: 80px;" class="py-3">
                                                <a href="{{ route('products.show', $item->product->slug) }}">
                                                    <img src="{{ asset($item->product->primary_image_path) }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="rounded border" 
                                                         style="width: 60px; height: 60px; object-fit: cover; border-color: var(--fs-border) !important;">
                                                </a>
                                            </td>
                                            {{-- Product Name and Franchise --}}
                                            <td class="py-3">
                                                <a href="{{ route('products.show', $item->product->slug) }}" class="text-decoration-none text-reset fw-semibold d-block mb-1">
                                                    {{ $item->product->name }}
                                                </a>
                                                @if($item->product->franchise)
                                                    <span class="franchise-badge">{{ $item->product->franchise }}</span>
                                                @endif
                                            </td>
                                            {{-- Price --}}
                                            <td class="py-3 text-center">
                                                <span class="fw-semibold">{{ number_format($item->product->price, 2) }} ₺</span>
                                            </td>
                                            {{-- Quantity selector --}}
                                            <td class="py-3">
                                                <div class="input-group input-group-sm justify-content-center">
                                                    <button class="btn btn-outline-secondary px-2 border-secondary btn-qty-change" 
                                                            type="button" 
                                                            data-id="{{ $item->id }}" 
                                                            data-action="decrease"
                                                            style="color: var(--fs-text);">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="text" 
                                                           class="form-control text-center bg-transparent border-secondary qty-input" 
                                                           value="{{ $item->quantity }}" 
                                                           readonly 
                                                           style="max-width: 50px; color: var(--fs-text);" 
                                                           id="qty-{{ $item->id }}">
                                                    <button class="btn btn-outline-secondary px-2 border-secondary btn-qty-change" 
                                                            type="button" 
                                                            data-id="{{ $item->id }}" 
                                                            data-action="increase"
                                                            data-max="{{ $item->product->stock }}"
                                                            style="color: var(--fs-text);">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            {{-- Item Subtotal --}}
                                            <td class="py-3 text-end">
                                                <span class="fw-bold item-subtotal" id="subtotal-{{ $item->id }}">
                                                    {{ number_format($item->quantity * $item->product->price, 2) }} ₺
                                                </span>
                                            </td>
                                            {{-- Remove Button --}}
                                            <td class="py-3 text-center">
                                                <button class="btn btn-link text-danger p-0 btn-remove-item" 
                                                        type="button" 
                                                        data-id="{{ $item->id }}"
                                                        title="Ürünü Sepetten Çıkar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-accent-outline">
                            <i class="fas fa-chevron-left me-2"></i>Alışverişe Devam Et
                        </a>
                    </div>
                </div>

                {{-- Right: Checkout Summary --}}
                <div class="col-lg-4">
                    <div class="fs-card p-4">
                        <h4 class="h5 mb-4 fw-bold">Sipariş Özeti</h4>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Ara Toplam</span>
                            <span class="fw-semibold" id="cartTotal">{{ number_format($total, 2) }} ₺</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Kargo</span>
                            <span class="text-success fw-semibold">Ücretsiz 🎉</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 border-bottom pb-3" style="border-color: var(--fs-border) !important;">
                            <span class="text-success fw-medium">Hesap Bakiyesi</span>
                            <span class="text-success fw-bold">{{ number_format($balance, 2) }} ₺</span>
                        </div>

                        @php
                            $remaining_amount = max(0, $total - $balance);
                            $balance_to_use = min($balance, $total);
                        @endphp

                        @if($balance > 0)
                            <div class="d-flex justify-content-between mb-3 text-success">
                                <span>Bakiyeden Düşülecek</span>
                                <span class="fw-semibold" id="balanceUsed">-{{ number_format($balance_to_use, 2) }} ₺</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Ödenecek Tutar</span>
                            <span class="h5 mb-0 fw-extrabold text-danger" id="payableAmount">{{ number_format($remaining_amount, 2) }} ₺</span>
                        </div>

                        @if($remaining_amount > 0 && $balance > 0)
                            <div class="alert alert-info py-2 px-3 small border-0 mb-4" style="background: rgba(13, 110, 253, 0.08); color: #0d6efd;">
                                <i class="fas fa-info-circle me-1"></i> {{ number_format($balance, 2) }} ₺ bakiyeniz düşüldükten sonra kalan tutar kredi kartı ile ödenecektir.
                            </div>
                        @elseif($remaining_amount == 0 && $balance > 0)
                            <div class="alert alert-success py-2 px-3 small border-0 mb-4" style="background: rgba(25, 135, 84, 0.08); color: #198754;">
                                <i class="fas fa-check-circle me-1"></i> Sipariş tutarının tamamı hesap bakiyenizden karşılanmaktadır.
                            </div>
                        @endif

                        <a href="{{ route('checkout.index') }}" class="btn btn-accent w-100 py-2.5 fw-bold">
                            Siparişi Tamamla<i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Quantity change listeners
    document.addEventListener('click', function(e) {
        // Find closest button with btn-qty-change class
        const btn = e.target.closest('.btn-qty-change');
        if (!btn) return;
        
        const id = btn.getAttribute('data-id');
        const action = btn.getAttribute('data-action');
        const input = document.getElementById('qty-' + id);
        let currentVal = parseInt(input.value) || 1;
        
        if (action === 'increase') {
            const max = parseInt(btn.getAttribute('data-max')) || 10;
            if (currentVal >= max) {
                showAlert('Stokta daha fazla ürün yok.', 'error');
                return;
            }
            if (currentVal >= 10) {
                showAlert('Maksimum 10 adet ekleyebilirsiniz.', 'error');
                return;
            }
            currentVal++;
        } else {
            if (currentVal <= 1) return;
            currentVal--;
        }
        
        updateQuantity(id, currentVal);
    });

    // Remove item listener
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-remove-item');
        if (!btn) return;
        
        const id = btn.getAttribute('data-id');
        if (confirm('Bu ürünü sepetinizden kaldırmak istediğinize emin misiniz?')) {
            removeItem(id);
        }
    });

    // Update quantity via AJAX
    function updateQuantity(id, qty) {
        fetch(`/cart/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: qty })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update input
                document.getElementById('qty-' + id).value = qty;
                
                // Update item subtotal
                document.getElementById('subtotal-' + id).innerText = formatCurrency(data.subtotal) + ' ₺';
                
                // Update checkout summary
                updateSummary(data.total);
                
                // Update navbar cart count
                updateNavbarCartCount();
            } else {
                showAlert(data.message || 'Bir hata oluştu.', 'error');
            }
        })
        .catch(err => {
            showAlert('Sunucuyla iletişim kurulurken bir hata oluştu.', 'error');
        });
    }

    // Remove item via AJAX
    function removeItem(id) {
        fetch(`/cart/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove item row
                const row = document.getElementById('cart-item-' + id);
                row.remove();
                
                // Update checkout summary
                updateSummary(data.total);
                
                // Update navbar cart badge
                const cartBadge = document.querySelector('.cart-badge');
                if (data.cartCount > 0) {
                    if (cartBadge) {
                        cartBadge.innerText = data.cartCount;
                    } else {
                        // Create badge if not exist
                        location.reload(); // Quickest way to render layout badge
                    }
                } else {
                    if (cartBadge) cartBadge.remove();
                    // If cart is empty, reload page to show empty state
                    location.reload();
                }
            } else {
                showAlert(data.message || 'Bir hata oluştu.', 'error');
            }
        })
        .catch(err => {
            showAlert('Sunucuyla iletişim kurulurken bir hata oluştu.', 'error');
        });
    }

    // Update prices summary
    function updateSummary(total) {
        document.getElementById('cartTotal').innerText = formatCurrency(total) + ' ₺';
        
        const balance = parseFloat("{{ $balance }}") || 0;
        const balanceUsedEl = document.getElementById('balanceUsed');
        
        const balanceToUse = Math.min(balance, total);
        if (balanceUsedEl) {
            balanceUsedEl.innerText = '-' + formatCurrency(balanceToUse) + ' ₺';
        }
        
        const payable = Math.max(0, total - balanceToUse);
        document.getElementById('payableAmount').innerText = formatCurrency(payable) + ' ₺';
    }

    function formatCurrency(val) {
        return parseFloat(val).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    function updateNavbarCartCount() {
        // Optional: Fetch navbar cart count via a light request or calculate in JS
        // Since we know the quantity has changed, we can calculate total items or do page refresh
        // But let's just query and count quantities in client side table rows
        let sum = 0;
        document.querySelectorAll('.qty-input').forEach(input => {
            sum += parseInt(input.value) || 0;
        });
        const badge = document.querySelector('.cart-badge');
        if (sum > 0) {
            if (badge) badge.innerText = sum;
        } else {
            if (badge) badge.remove();
        }
    }

    function showAlert(message, type) {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `
            <div class="alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show border-0 shadow-sm position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1080;">
                <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
            </div>
        `;
        document.body.appendChild(wrapper);
        setTimeout(() => {
            const alert = wrapper.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 4000);
    }
});
</script>
@endpush
