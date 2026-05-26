# 🎬 FanStore — Film & Dizi Merchandise Platformu

> Kocaeli Üniversitesi TBL304 Web Programlama Dersi Projesi

## 📖 Proje Hakkında

FanStore, popüler film ve TV dizilerine ait lisanslı
ürünlerin (poster, figür, giyim, aksesuar vb.)
satışını gerçekleştiren Laravel 11 tabanlı
e-ticaret platformudur.

## ✨ Özellikler

### 🛍️ Mağaza
- Koyu/Açık tema toggle (localStorage)
- Ürün listeleme, filtreleme, arama, sıralama
- Ürün detay sayfası + TMDB film/dizi bilgisi
- Franchise bazlı keşif

### 🛒 Alışveriş
- AJAX sepet sistemi
- Parçalı ödeme (önce bakiye, kalan kart)
- Stok kontrolü

### 📦 Sipariş Takip
- 5 aşamalı animasyonlu kargo takibi
- Sipariş iptali → bakiyeye otomatik iade
- Hava durumu kargo gecikme uyarısı
- "Teslim Aldım" onay sistemi

### 👤 Kullanıcı
- Profil yönetimi + WebP avatar
- Bakiye & transaction geçmişi
- Adres defteri + Google Maps
- Hesap pasifleştirme

### 🎛️ Admin Paneli
- Ürün CRUD + WebP görsel yükleme
- TMDB Quick Search entegrasyonu
- Kullanıcı yönetimi
- Sipariş onaylama + kargo takibi

### 🔌 API Entegrasyonları
- TMDB API (film/dizi bilgisi)
- OpenWeatherMap (kargo gecikme uyarısı)
- Google Maps Places API (adres otomatik tamamlama)

## 🛠️ Teknoloji Yığını

- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Bootstrap 5.3, AdminLTE 3
- **Veritabanı:** MySQL 8
- **Görsel İşleme:** Intervention Image v3 (WebP)
- **Deploy:** Railway.app

## 🚀 Kurulum

### Gereksinimler
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL 8

### Adımlar

1. Repoyu klonla:
git clone https://github.com/emirhanok1/fan-shop-web-programming-project.git
cd fan-shop-web-programming-project

2. Bağımlılıkları yükle:
composer install
npm install

3. .env dosyasını oluştur:
cp .env.example .env
php artisan key:generate

4. Veritabanını ayarla:
.env dosyasında DB_* değerlerini doldur
php artisan migrate --seed

5. Storage link:
php artisan storage:link

6. Frontend derle:
npm run build

7. Sunucuyu başlat:
php artisan serve

## 🔑 Demo Hesapları

| Rol | Email | Şifre |
|-----|-------|-------|
| Admin | admin@fanstore.com | admin123 |
| Kullanıcı | user1@fanstore.com | password |

## 🌐 Canlı Demo

[FanStore Demo](https://your-domain.up.railway.app)

## 📋 API Anahtarları

Aşağıdaki API anahtarlarını .env dosyasına ekle:
- TMDB_API_KEY: https://www.themoviedb.org/settings/api
- OPENWEATHER_API_KEY: https://openweathermap.org/api
- GOOGLE_MAPS_API_KEY: https://console.cloud.google.com

## 📊 Veritabanı Şeması

11 tablo: users, categories, products,
product_images, carts, cart_items, orders,
order_items, order_tracking, transactions, addresses

## 👨‍💻 Geliştirici

Emirhan Oktay — Kocaeli Üniversitesi
Bilişim Sistemleri Mühendisliği
