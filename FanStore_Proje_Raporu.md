# FanStore — Proje Başlangıç Raporu
**Film ve Dizi Lisanslı Ürünleri (Merchandise) Satış Platformu**

---

## 1. Proje Kimliği

| Alan | Bilgi |
|---|---|
| Proje Adı | FanStore |
| Ders | TBL304 Web Programlama — 2025-2026 Bahar |
| Üniversite | Kocaeli Üniversitesi, Bilişim Sistemleri Mühendisliği |
| Konu | Film ve Dizi Lisanslı Ürünleri (Merchandise) Satış Sitesi |
| Rapor Teslim | 24 Mayıs 2026 Pazar 23:59 |
| Sunum | 25 Mayıs / 1 Haziran / 8 Haziran 2026 |

---

## 2. Proje Özeti

FanStore, popüler film ve TV dizilerine ait lisanslı ürünlerin (poster, figür, giyim, aksesuar vb.) satışını gerçekleştiren, Laravel 11 MVC mimarisi üzerine inşa edilmiş tam kapsamlı bir e-ticaret platformudur. Platform; Admin ve User olmak üzere iki rol üzerinden çalışır, TMDB API entegrasyonu ile ürün sayfalarına dinamik film/dizi verisi çeker, Google Maps API ile adres doğrulama yapar ve OpenWeatherMap API ile kargo takip sayfasında hava durumu uyarısı gösterir.

---

## 3. Teknoloji Yığını

### Backend
| Teknoloji | Versiyon | Kullanım Amacı |
|---|---|---|
| PHP | 8.2+ | Temel programlama dili |
| Laravel | 11.x | MVC framework |
| MySQL | 8.x | İlişkisel veritabanı |
| Laravel Breeze | Güncel | Auth sistemi (Blade stack) |
| Intervention Image | v3 (image-laravel) | Görsel yükleme ve WebP dönüşümü |

### Frontend
| Teknoloji | Versiyon | Kullanım Amacı |
|---|---|---|
| Bootstrap | 5.3 | Responsive UI framework |
| AdminLTE | 3.x (modüler CSS) | Admin panel şablonu |
| Vite | Güncel | Asset derleme |
| FontAwesome | 5.x CDN | İkon kütüphanesi |
| jQuery | 3.6 CDN | AdminLTE bağımlılığı |

### API Entegrasyonları
| API | Kullanım Amacı | Cache Süresi |
|---|---|---|
| TMDB API v3 | Ürün sayfasında film/dizi bilgisi, admin panel hızlı arama | 24 saat |
| Google Maps JS API (New Places) | Checkout adres otomatik tamamlama, harita gösterimi | — |
| OpenWeatherMap API | Kargo takip sayfasında hava durumu uyarısı | 1 saat |

### Deployment
| Araç | Kullanım Amacı |
|---|---|
| Railway.app | Üretim hosting (PHP + MySQL + Volume) |
| GitHub | Kaynak kod yönetimi ve CI/CD |
| Railway Volume | Storage kalıcılığı (/app/storage) |

---

## 4. Mimari Kararlar ve Gerekçeleri

### 4.1 Laravel 11 Seçimi
CodeIgniter 4 yerine Laravel 11 seçilmesinin teknik gerekçeleri:
- **Eloquent ORM** — Karmaşık sipariş/bakiye/tracking ilişkileri için üstün
- **Yerleşik Auth** (Breeze) — Admin/User rolleri hızlıca kurulabilir
- **Service Container** — TMDB, Weather, Maps servislerini temiz inject eder
- **Laravel Cache** — API yanıtlarını `Cache::remember()` ile otomatik önbelleğe alır
- **Middleware sistemi** — `bootstrap/app.php` üzerinden merkezi yönetim (Laravel 11 yeni mimarisi)

### 4.2 AdminLTE 3 + Bootstrap 5 Entegrasyonu
AdminLTE 3, Bootstrap 4 tabanlıdır. Bootstrap 5 ile çakışmayı önlemek için:
- Monolitik `adminlte.min.css` **kullanılmaz**
- `dist/css/alt/adminlte.core.min.css` ve `adminlte.components.min.css` modüler dosyaları kullanılır
- Tüm `data-toggle` → `data-bs-toggle` dönüşümü yapılır
- Click delay (Issue #4527) için özel DOM fix uygulanır

### 4.3 TMDB API — Bearer Token
Araştırma sonucuna göre TMDB API key yerine Bearer Token zorunlu:
```
Authorization: Bearer {API_READ_ACCESS_TOKEN}
```
TV dizileri için `name` alanı, filmler için `title` alanı kullanılır.

### 4.4 Google Maps — Yeni API
Eski `Autocomplete` sınıfı deprecated — Yeni `PlaceAutocompleteElement` kullanılır:
```javascript
const { PlaceAutocompleteElement } = await google.maps.importLibrary("places");
autocomplete.addEventListener('gmp-select', async ({ placePrediction }) => { ... });
```

### 4.5 Railway Deploy Kritik Ayarlar
```toml
# nixpacks.toml
[variables]
NIXPACKS_PHP_ROOT_DIR = '/app/public'
NIXPACKS_PHP_FALLBACK_PATH = '/index.php'
```
- Volume mount: `/app/storage`
- `LOG_CHANNEL=stderr`
- `AppServiceProvider`'da `URL::forceScheme('https')`

---

## 5. Veritabanı Şeması (Tablolar)

| Tablo | Açıklama |
|---|---|
| users | id, name, email, password, role(admin/user), balance, is_active, phone, avatar |
| categories | id, name, slug, description, image |
| products | id, category_id, name, slug, description, price, stock, is_active, tmdb_id, franchise |
| product_images | id, product_id, image_path, is_primary |
| carts | id, user_id |
| cart_items | id, cart_id, product_id, quantity |
| orders | id, user_id, total_amount, balance_used, card_amount, status, shipping_address, invoice_no |
| order_items | id, order_id, product_id, quantity, unit_price |
| order_tracking | id, order_id, step(sourcing/packaging/shipped/on_the_way/delivered) |
| transactions | id, user_id, order_id, amount, type(payment/refund/bonus), description |
| addresses | id, user_id, title, full_address, city, district, zip, lat, lng, is_default |

**Normalizasyon:** Tüm tablolar 3NF (Üçüncü Normal Form) kurallarına uygundur.

---

## 6. Kullanıcı Rolleri ve Yetkileri

### Admin
- Ürün CRUD (çoklu fotoğraf yükleme, WebP dönüşümü)
- TMDB Quick Search ile ürün bilgisi otomatik doldurma
- Satışa açma/kapatma, stok yönetimi
- Kullanıcı yönetimi (görüntüleme, dondurma, silme)
- Sipariş onaylama ve 5 aşamalı tracking ilerleme (İleri butonu)
- Kendi profil yönetimi

### User
- Kayıt, giriş, profil güncelleme, şifre sıfırlama
- Ürün listeleme, filtreleme, detay görüntüleme
- Sepet yönetimi (ekle, çıkar, miktar güncelle)
- Ödeme: önce bakiyeden, kalan kredi kartından
- Sipariş takibi (5 aşama)
- Admin onayı öncesi sipariş iptali (iade → bakiyeye)
- Adres defteri yönetimi
- Üyeliği pasif etme

---

## 7. Sipariş Durumu (State Machine)

```
pending → [Admin Onaylar] → sourcing → packaging → shipped → on_the_way → delivered → [User Onaylar] → confirmed
                                                                                              ↑
                                                                               "Teslim Aldım" butonu aktif
```

| Durum | Kullanıcı İptal | Admin İşlemi | Kullanıcı Butonu |
|---|---|---|---|
| pending | ✅ Aktif (iade→bakiye) | Onayla / Reddet | — |
| sourcing | ❌ Pasif | İleri | — |
| packaging | ❌ Pasif | İleri | — |
| shipped | ❌ Pasif | İleri | — |
| on_the_way | ❌ Pasif | İleri | — |
| delivered | ❌ Pasif | — | Teslim Aldım ✅ |

---

## 8. Bakiye (Wallet) Sistemi

```
Sipariş tutarı: 500₺
Kullanıcı bakiyesi: 150₺

→ balance_used: 150₺ (önce bakiyeden)
→ card_amount: 350₺ (kredi kartından)
→ Transactions kaydı: type=payment, amount=-150
```

**İptal durumunda:**
```
→ users.balance += order.total_amount
→ Transactions kaydı: type=refund, amount=+500
→ Kredi kartına iade YOK
```

---

## 9. API Kullanım Detayları

### TMDB API
- **Endpoint (TV):** `GET /tv/{series_id}`
- **Endpoint (Film):** `GET /movie/{movie_id}`
- **Arama:** `GET /search/tv?query={q}` veya `/search/movie?query={q}`
- **Auth:** `Authorization: Bearer {TMDB_READ_ACCESS_TOKEN}`
- **Cache:** `Cache::remember('tmdb_'.$id, 86400, fn() => ...)`
- **Poster URL:** `https://image.tmdb.org/t/p/w500{poster_path}`

### OpenWeatherMap
- **Endpoint:** `GET /weather?q={city}&appid={key}&lang=tr&units=metric`
- **Cache:** 1 saat (`Cache::remember('weather_'.Str::slug($city), 3600, ...)`)
- **Uyarı koşulu:** condition = Rain, Storm, Snow, Thunderstorm

### Google Maps
- **Kullanım:** Checkout sayfasında adres otomatik tamamlama
- **API:** Places API (New) — `PlaceAutocompleteElement`
- **fetchFields:** `['formattedAddress', 'location', 'addressComponents']`
- **Key güvenliği:** HTTP Referrer kısıtlaması zorunlu

---

## 10. Geliştirme Fazları

| Faz | Konu | Tahmini Süre |
|---|---|---|
| 0 | .gitignore + Rules + Proje Raporu | ✅ Tamamlandı |
| 1 | Laravel Kurulum + DB Tasarımı + Migrations + Seeders | 1-2 gün |
| 2 | Auth Sistemi + Admin Middleware + Roller | 1 gün |
| 3 | Admin Paneli — Ürün & Kullanıcı Yönetimi | 2 gün |
| 4 | Kullanıcı Frontend — Mağaza & Ürünler | 2 gün |
| 5 | Sepet & Ödeme & Bakiye Sistemi | 2 gün |
| 6 | Sipariş Takip Sistemi (State Machine) | 1 gün |
| 7 | API Entegrasyonları (TMDB + Maps + Weather) | 1-2 gün |
| 8 | Test, Deploy (Railway) & GitHub | 2 gün |
| 9 | IEEE Rapor + ER & Akış Diyagramları | 2 gün |

---

## 11. Coding Rules Özeti

### Zorunlu
- Laravel 11 syntax: `bootstrap/app.php` middleware (Kernel.php yok)
- `config()` helper kullan, `env()` direkt kullanma
- `File::image()` ile doğrulama (mimes yeterli değil)
- UUID ile dosya isimlendirme
- `@csrf` her formda zorunlu
- Tüm controller'lar Form Request kullanır

### Yasak
- `data-toggle` → `data-bs-toggle` kullan
- `ml-*`, `mr-*` → `ms-*`, `me-*` kullan
- `adminlte.min.css` (monolitik) kullanma
- API key'i frontend JS'e direkt yazma
- Original filename ile dosya kaydetme

---

## 12. Seeder Verileri

| Veri | Miktar |
|---|---|
| Admin | 1 (admin@fanstore.com / admin123) |
| User | 5 (user1-5@fanstore.com / password) |
| Kategori | 5 (Posterler, Figürler, Giyim, Mutfak, Çanta) |
| Ürün | 20 (Breaking Bad, GoT, Witcher, Peaky Blinders, Stranger Things, Harry Potter, Marvel, Star Wars) |

---

## 13. Teslim Kontrol Listesi

- [ ] Min. 1 Admin, 5 User, 20 Ürün seeder
- [ ] Admin paneli (ürün, kullanıcı, sipariş yönetimi)
- [ ] Kullanıcı paneli (profil, sepet, sipariş, bakiye)
- [ ] 5 aşamalı sipariş takibi
- [ ] Bakiye sistemi (iade → bakiyeye)
- [ ] En az 2 Web API entegrasyonu
- [ ] Google Maps (iframe değil, native API)
- [ ] Bootstrap responsive tasarım
- [ ] Canlı sunucu (Railway.app)
- [ ] GitHub repo + README.md
- [ ] IEEE rapor (4-8 sayfa, ERD + Akış Diyagramı)
- [ ] Veritabanı yedeği (.sql)
- [ ] Google Drive paylaşım linki

---

*Rapor oluşturulma tarihi: Mayıs 2026*
*Proje: TBL304 Web Programlama — FanStore Merchandise Platform*
