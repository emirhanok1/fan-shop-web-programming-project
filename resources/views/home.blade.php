<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>FanStore - Anasayfa</title>
</head>
<body>
    <h1>FanStore Anasayfa (Faz 2)</h1>
    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif
    @auth
        <p>Giriş Yaptınız: {{ auth()->user()->name }} (Rol: {{ auth()->user()->role }})</p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Çıkış Yap</button>
        </form>
    @else
        <a href="{{ route('login') }}">Giriş Yap</a> | 
        <a href="{{ route('register') }}">Kayıt Ol</a>
    @endauth
</body>
</html>
