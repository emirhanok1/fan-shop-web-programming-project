<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>FanStore - Admin Paneli</title>
</head>
<body>
    <h1>FanStore Admin Paneli Dashboard (Faz 2)</h1>
    <p>Giriş Yapan Admin: {{ auth()->user()->name }}</p>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Çıkış Yap</button>
    </form>
</body>
</html>
