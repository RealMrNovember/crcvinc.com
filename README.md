# CRC Vinç — Kurumsal Web Sitesi

CRC Vinç Kiralama Yedek Parça İthalat İhracat San. Tic. Ltd. Şti. için geliştirilen kurumsal web sitesi ve içerik yönetim paneli.

**Canlı site:** [crcvinc.com](https://www.crcvinc.com) · [crcvinc.com.tr](https://www.crcvinc.com.tr)

---

## Proje Hakkında

Vinç kiralama, ağır kaldırma ve yedek parça tedariki alanında hizmet veren CRC Vinç için sıfırdan tasarlanmış, framework ve veritabanı bağımlılığı olmayan hafif bir kurumsal site. Paylaşımlı hosting ortamlarında sorunsuz çalışacak, bakım yükü minimum bir mimariyle inşa edildi.

Projenin en önemli önceliği: **müşterinin geliştiriciye ihtiyaç duymadan siteyi yönetebilmesi.** Hero videosundan makine parkına, referanslardan iletişim bilgilerine kadar site içeriğinin tamamı özel bir yönetim panelinden düzenlenebiliyor.

### Öne Çıkan Özellikler

- **Tam ekran, responsive hero videosu** — YouTube üzerinden sessiz/döngülü oynatma, tüm ekran boyutlarında (mobil → masaüstü) kırpılma veya taşma olmadan tam kaplama
- **Uçtan uca yönetilebilir içerik** — menü, hizmetler, makine parkı, projeler, referanslar, sayaçlar, güvenlik/İSG şeridi, sayfa metinleri ve iletişim bilgileri panelden değiştirilebiliyor
- **Güvenli, tek kullanıcılı admin paneli** — bcrypt şifreleme, CSRF koruması, oturum bazlı giriş sınırlama; kullanıcı panelden kendi şifresini değiştirebiliyor
- **Doğrulamalı iletişim formu** — honeypot ile bot koruması, e-posta gönderimi başarısız olsa dahi mesajlar kayıt altında
- **Scroll-reveal animasyonlar, animasyonlu sayaçlar, proje karuseli** — sektör beklentisine uygun modern, akıcı bir kullanıcı deneyimi
- **Sıfır veritabanı** — tüm içerik `data/site.json` içinde saklanıyor; kurulum ve taşıma son derece basit

## Teknoloji

| Katman | Teknoloji |
|---|---|
| Backend | Saf PHP 8+ (framework yok) |
| İçerik deposu | JSON dosyası (veritabanı yok) |
| Frontend | Vanilla CSS + JavaScript (kütüphane bağımlılığı yok) |
| Hosting | DirectAdmin / paylaşımlı hosting uyumlu |

## Proje Yapısı

```
public_html/        ← web root (hosting'de domains/crcvinc.com/public_html)
  index.php          front controller (tüm sayfalar)
  admin/              yönetim paneli
  assets/             css / js / görseller
app/                 ← şablonlar ve yardımcılar (web root dışına yüklenir)
data/                ← site.json — tüm site içeriği (web root dışına yüklenir)
docs/                ← piyasa araştırması ve deploy dokümantasyonu
router.php           ← yalnızca lokal geliştirme için
```

## Yerel Geliştirme

```bash
php -S localhost:8080 router.php
```

## Dağıtım (DirectAdmin / FTP)

`app/` ve `data/` klasörleri `public_html`'in **dışına**, `public_html/` içeriği ise hosting'in web root'una yüklenir. Ayrıntılı adımlar için [docs/deploy.md](docs/deploy.md).

## Yönetim Paneli

`/admin/` adresi ilk açılışta kurulum ekranı sunar ve kalıcı giriş bilgileri oluşturur. Panelden yönetilebilenler:

Hero videosu ve metinleri · Menü · Sayaçlar · Hizmetler · Makine parkı · Projeler · Referanslar · Bölüm başlıkları · Sayfa metinleri · İletişim bilgileri · Hesap (kullanıcı adı/şifre)

---

## Geliştirici

Bu proje **[CiciByte Teknoloji](https://cicibyte.com)** tarafından tasarlanmış ve geliştirilmiştir.

CiciByte Teknoloji, işletmelere özel web sitesi, yönetim paneli ve dijital altyapı çözümleri geliştiren bir yazılım stüdyosudur.

**Web:** [cicibyte.com](https://cicibyte.com)

---

© CRC Vinç Kiralama Yedek Parça İthalat İhracat San. Tic. Ltd. Şti. Tüm hakları saklıdır. Bu kaynak kod CRC Vinç için özel olarak geliştirilmiştir.
