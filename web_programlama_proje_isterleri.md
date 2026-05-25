**Kocaeli Üniversitesi Teknoloji Fakültesi Bilişim Sistemleri Mühendisliği Bölümü TBL304: Web Programlama Dersi 2025-2026 Bahar Yarıyılı Web Projesi İçerik Yönetim Sistemine Sahip Web Tabanlı Alış-Veriş Sitesi Geliştirme** 

## **1. Amaç** 

Laravel ve CodeIgniter MVC frameworklerinden birini kullanarak İçerik Yönetim Sistemine sahip bir alış-veriş sitesi (ürünün tanıtımı, satışı, siparişi vb) gerekli tüm aşamaları geliştirmeniz beklenmektedir. 

**Rapor** IEEE formatında en az 4 ve en fazla 8 sayfa, akış diyagramı, varlık-ilişki diyagramı, UML diyagramı veya sözde kod içeren, özet, giriş, yöntem, deneysel sonuçlar, kazanımlarınız, sonuç ve kaynakça bölümünden oluşmalıdır. Raporda AKIŞ DIYAGRAMI ve VARLIK-İLİŞKİ DIYAGRAMI mutlaka olmalıdır. 

Sunum sırasında algoritma, geliştirdiğiniz kodun çeşitli kısımlarının ne amaçla yazıldığı ve geliştirme ortamı hakkında sorular sorulabilir. Kullandığınız herhangi bir satır kodu açıklamanız istenebilir. Proje TEK kişiliktir. Projeye başlamadan derste seçtiğiniz konu hakkında benimle 

görüşmelisiniz, onay aldıktan sonra burayı tıklayarak Excel listesini doldurarak 

projenize başlayabilirsiniz. Proje Konusu Belirleme İçin Son Tarih **Son Tarih: 16 Mart 2025 Pazar Günü 23:59'dur.** 

Örnek Olarak İnceleyebileceğiniz Web Siteleri 

- Alış-veriş siteleri 

- Teknolojik ürün satışı yapan siteler (Bilgisayar, Parça, vs.) 

- Tatil hizmeti satan siteler 

- Araç satımı, kiralaması yapan siteler 

- Evcil hayvan malzemeleri satan siteler 

- Eğitim hizmeti satan siteler 

## **2. Notlandırma** 

**Yorum** : Aşağıdaki notlandırma bilgisi projeyi yaparken planlama yapabilmeniz açısından paylaşılmıştır. Bir puanlama kaleminden sunum değerlendirmesine göre tam puan alınabileceği gibi eksik puan da alınabilir. Sunumda sorulara verilen cevaplar ve kod hakimiyeti kriterlerin içerisinde değerlendirilecektir. 

Doğru değerlendirme yapabilmek için isterlerin yerine getirildiğini gösterebilmeniz gerekmektedir. 

**2.1. Proje Konusu** : Ürün satışlarının yapıldığı web tabanlı bir alış/veriş sitesi yapmanız istenmektedir. Günümüzde, online alış/veriş siteleri ve mobil uygulamaları insanların rahat ve her yerden istedikleri anda sipariş vererek ihtiyaçlarını karşıladıkları uygulamalar geliştirilmektedir. Herhangi bir sektör’ü seçerek bu sektöre ait web tabanlı bir alışveriş sitesi yapmanız beklenmektedir. Geliştireceğiniz web sitesi; ürün tanıtımının olduğu önyüzü olan, admin panelinin olduğu arka yüzü olan, yönetici (admin) ve kullanıcı (user) rollerinin olduğu ve kullanıcılarının oturum açarak alışveriş sepetine ürün eklediği, yöneticilerin alışveriş için ürün eklediği mvc tabanlı bir alışveriş sitesi olmalıdır. 

1 

**Kocaeli Üniversitesi Teknoloji Fakültesi Bilişim Sistemleri Mühendisliği Bölümü TBL304: Web Programlama Dersi 2025-2026 Bahar Yarıyılı Web Projesi İçerik Yönetim Sistemine Sahip Web Tabanlı Alış-Veriş Sitesi Geliştirme** 

## **3. Proje İsterleri (Projenizde Olması Gerekenler)** 

**3.1. Rapor** : İçeriği, akış diyagramı, kazanımlarınız, yorumlarınız dikkate alınacaktır. Sunumun yapılması. Rapor içeriğine, giriş kısmında ve bir üst kısımda genel hatlarıyla değinilmiştir. İki tip kullanıcı olacaktır. **Admin** ve **User** 

Kullanıcı Ekleme, Kullanıcı Oturum Açma, Kullanıcı Bilgilerini Güncelleme (eposta adresini değiştirme, şifre sıfırmala, ad-soyad değiştirme, adresini ekleme, düzenleme vb.). **Admin** : sistemi kontrol edecek/yönetecek. 

Ürün Ekleyecek. 

Ürün Bilgisi, Adeti, Fiyatı Girilecek/Güncellenecek/Silinecek 

Ürüne ait Fotoğraf ya da Fotoğraflar yükleyecek/güncelleyecek/silecek 

Ürünleri Satışa Sunacak/Satıştan Kaldıracak, Ürün stok bilgisini kontrol ederek ürünler satılacak 

Ürünün ücretini alacak ve faturalandırma yaparak ürünü kargoya hazır hale getirecek. Ürün siparişlerini kargoya verecek, ürünün teslimatını takip edip teslim edecek. 

Sistemdeki kendine ait bilgileri: Görüntüleyecek, Güncelleyebilecek, Şifre sıfırlayacak Kullanıcıları Yöntecek: Görüntüleme/Güncelleme/Silme/Hesap Dondurma 

Siparişleri Takip Edecek: Görüntüleme/Siparişi Onaylama 

**User(kullanıcı)** : sisteme Oturum Açacak ya da Kayıt Olacak 

Sistemdeki kendine ait bilgileri: Görüntüleyecek, Güncelleyebilecek, Şifre sıfırlayacak, Ürünleri Görüntüleyecek, Ürünlerin Ayrıntılarını Görüntüleyecek 

Ürünleri sepetine ekleyecek, sepetten ürün çıkarabilecek, toplam tutarı görecek, kredi kartı ile ödeme ekranında ödemesini yapacak, sipariş bilgilerini girecek ve sipariş verecek. 

User (kullanıcı), Siparişinin durumunu takip edebilecek. Siparişini, Admin onaylamadıysa iptal edebilecek ve siparişten kalan ücreti hediye olarak alışveriş sitesindeki hesabına geri yatırılacak (Kredi kartı hesabına geri yatırılmayacak). Alışveriş sitesindeki kullanıcı Hesabında, kullanıcı bakiyesinde bu tutarlar gözükmelidir. Kullanıcı alışveriş yaparken ilk önce hesabındaki bu bakiyeden harcama yapmalıdır. Kullanıcının siparişini admin onayladığı zaman, kullanıcı siparişini iptal edemeyecek ve siparişinin hazırlanış aşamalarını (ürünleriniz tedarik ediliyor, ürünleriniz kutulanıyor, ürünleriniz kargoya veriliyor, ürünleriniz size doğru yola çıktı ve ürünleriniz size teslim edilmiştir.) takip edebileceği bir sürece geçecektir. Ürünler kullanıcının eline geçtiğinde, kullanıcı siparişini takip ettiği sayfadan ürünlerimi teslim aldım butonuna tıklayacak ve siparişi teslim almış olacaktır. Bu buton,   “ürünleriniz size teslim edilmiştir” aşamasından sonra aktif olacaktır. Admin, ürünlerin hazırlanış aşamalarını (ürünleriniz tedarik ediliyor, ürünleriniz kutulanıyor, ürünleriniz kargoya veriliyor, ürünleriniz size doğru yola çıktı ve ürünleriniz size teslim edilmiştir.) bir buton yardımıyla ileri ileri diyerek bu süreci ilerletecektir. 

Kullanıcı, Üyeliğini pasif edebilecek. 

Projenizde **Bootstrap** gibi **Responsive** bir template kullanılacak. **Proje ne amaçla geliştirildiyse amacına uygun çalışacak.** 

2 

**Kocaeli Üniversitesi Teknoloji Fakültesi Bilişim Sistemleri Mühendisliği Bölümü TBL304: Web Programlama Dersi 2025-2026 Bahar Yarıyılı Web Projesi İçerik Yönetim Sistemine Sahip Web Tabanlı Alış-Veriş Sitesi Geliştirme** 

**Projede Veritabanı işlemleri olmalıdır** . Kayıt ekleme, silme, güncelleme ve görüntüleme. **Veritabanı tasarımı** dikkate alınacaktır. 

Projede en az 1 Admin, 5 User ve 20 adet ürün bulunmalıdır. 

**Proje internette bir web sunucu üzerinden çalıştırılarak sunulacaktır.** 

Projenin Kodlarını ve açıklamasın da proje raporunuz olacak şekilde, Github da projenizi anlatarak paylaşmalısınız. 

Projede bir ya da daha fazla WEB API den bilgi alınıp işlem yapılmışsa (Günlük Hava Tahmini, Şehir ismi ile Sıcaklık, Basınç, Nemlilik, Gün Batımı Gün Doğumu, Şehrin Konumu ve Google Map'ten API aracılığıyla yapılacak, sadece iframe kullanımı kabul edilmeyecektir, Şehrin Haritasının gösterilmesi gibi). 

**==> picture [42 x 36] intentionally omitted <==**

## **4. Proje Teslim Süreci** 

Proje yazarken kullanacağınız IEEE formatındaki rapor şablonunun **Word halini . buraya tıklayarak indirebilirsiniz** 

**==> picture [47 x 48] intentionally omitted <==**

## **4.1. Proje Kaynak Kodunun Teslim Edilmesi** 

1. Proje kaynak kodunuz (Tüm kalör). 

2. Proje veri tabanı yedeğiniz. (sql uzantılı olacak). 

3. Proje Raporunuz (Word hali ve pdf hali). 

Yukarıdaki dosyaları bir klasörün içinde sıkıştırmalısınız. Sıkıştırılmış dosyanın ismi: “ **ogrencino_ad_soyad.rar/zip”** şekliden olmalıdır. GoogleDrive, DropBox, OneDrive vb. paylaşım sitesi aracılığıyla paylaşma bağlantısı oluşturulmalıdır. Paylaşma bağlantısı: bağlantıya sahip herkes tarafından indirilebilir olmalıdır, indirirken izin istememelidir. Bu bağlantı bilgisi Proje Raporunuzda yazmalıdır. E-destek sistemi üzerinden, sadece proje raporunuzu yüklemeniz istenecektir. Ben, proje raporunuzdaki paylaştığınız bağlantıya tıklayarak bütün proje klasörünü (bakınız: **Proje Kaynak Kodunun Teslim Edilmesi)** indireceğim. Klasörü indirirken paylaşma izni sıkıntısı yaşamak istemiyorum. Proje raporunu istenilen şekilde yüklemeyen veya indirme bağlantısına uygun bağlantı iznini vermeyen öğrenci, Proje NOTU **GİRMEDİ** olarak **NOTLANDIRILACAKTIR** ! 

Projenizin sunumu yaparak ve Proje Kaynak Kodlarını teslim ederek Projenizi tamamlamış sayılacaksınız. Bu aşamada eksikleri olan öğrenciler **NOTLANDIRILMAYACAKTIR!** Tüm bu süreçlerin düzgün bir şekilde işlemesi öğrencilerimizin sorumluluğundadır. 

## **4.2. Proje Sunum Tarihleri** 

- **25 Mayıs 2026** Pazartesi Ders Saatinde **09:00-13:00** Arası. 

- **01 Haziran 2026** Pazartesi Ders Saatinde **09:00-13:00** Arası. 

- **08 Haziran 2026** Pazartesi Ders Saatinde **09:00-13:00** Arası. 

3 

## **Kocaeli Üniversitesi Teknoloji Fakültesi Bilişim Sistemleri Mühendisliği Bölümü TBL304: Web Programlama Dersi 2025-2026 Bahar Yarıyılı Web Projesi İçerik Yönetim Sistemine Sahip Web Tabanlı Alış-Veriş Sitesi Geliştirme** 

Yukarıda belirtilen tarihler dışında sunum alınmayacaktır. Öğrenciler yukarıdaki sunum tarihlerine gör sunumları yapacaklardır. Proje Raporları **24 Mayıs 2026 Pazar Saat 23:59’a** kadar E-destek Sistemine Yüklenmelidir. Bu tarihten sonra proje gönderimi **KABUL EDİLMEYECEKTİR** . Öğrenciler, Rastgele sunuma kaldırılarak sunum yapacaklardır. **25 Mayıs 2026 Pazartesi** günü tüm öğrenciler her an sunuma kalkacaklarmış gibi hazırlıklarını tamamlamış olmalıdır. Proje Hazırlayıp Sunum Yapamayan Öğrenciler **Başarısız Sayılacaktır** . Belirtilen Tarihlerde Proje Sunumlarınızı Yapmalısınız. Ek süre veya bir tarih verilmeyecektir. Bu durum öğrencilerin sorumluluğundadır. 

Bu Belgedeki İsterlere göre EMEK harcadığınızı GÖSTERMENİZ ve bunu Proje Sunumunuzda HİSSETTİRMENİZ gerekecektir! 

## **Başarılar Dilerim.** 

4 

