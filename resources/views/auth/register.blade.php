@extends('layouts.app')

@section('title', 'Kayıt Ol - FanStore')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <h2 style="color: var(--fs-accent); font-weight: 800;">
                        <i class="fas fa-film me-2"></i>FanStore
                    </h2>
                </a>
                <p class="small" style="color: var(--fs-text-muted);">Yeni bir hesap oluşturun</p>
            </div>

            <div class="fs-card p-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold" style="color: var(--fs-text-muted);">Ad Soyad</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                               class="form-control fs-search-input @error('name') is-invalid @enderror"
                               required autofocus autocomplete="name"
                               placeholder="Adınız Soyadınız">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold" style="color: var(--fs-text-muted);">E-posta Adresi</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="form-control fs-search-input @error('email') is-invalid @enderror"
                               required autocomplete="username"
                               placeholder="ornek@mail.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label small fw-semibold" style="color: var(--fs-text-muted);">Şifre</label>
                        <input id="password" type="password" name="password"
                               class="form-control fs-search-input @error('password') is-invalid @enderror"
                               required autocomplete="new-password"
                               placeholder="••••••••">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label small fw-semibold" style="color: var(--fs-text-muted);">Şifre Tekrar</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-control fs-search-input @error('password_confirmation') is-invalid @enderror"
                               required autocomplete="new-password"
                               placeholder="••••••••">
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-accent w-100 py-2 fw-semibold">
                        <i class="fas fa-user-plus me-2"></i>Kayıt Ol
                    </button>
                </form>

                <div class="text-center mt-3">
                    <p class="small mb-0" style="color: var(--fs-text-muted);">
                        Zaten hesabınız var mı?
                        <a href="{{ route('login') }}" style="color: var(--fs-accent); text-decoration: none; font-weight: 600;">Giriş Yap</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
