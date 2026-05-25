@extends('layouts.app')

@section('title', 'Giriş Yap - FanStore')

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
                <p class="small" style="color: var(--fs-text-muted);">Hesabınıza giriş yapın</p>
            </div>

            <div class="fs-card p-4">
                {{-- Session Status --}}
                @if(session('status'))
                    <div class="alert alert-success alert-sm small mb-3">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold" style="color: var(--fs-text-muted);">E-posta Adresi</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="form-control fs-search-input @error('email') is-invalid @enderror"
                               required autofocus autocomplete="username"
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
                               required autocomplete="current-password"
                               placeholder="••••••••">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                            <label class="form-check-label small" for="remember_me" style="color: var(--fs-text-muted);">Beni Hatırla</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-accent w-100 py-2 fw-semibold">
                        <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                    </button>
                </form>

                <div class="text-center mt-3">
                    <p class="small mb-0" style="color: var(--fs-text-muted);">
                        Hesabınız yok mu?
                        <a href="{{ route('register') }}" style="color: var(--fs-accent); text-decoration: none; font-weight: 600;">Kayıt Ol</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
