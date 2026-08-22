# Deploy — DirectAdmin / FTP

## Dizin eşlemesi

Hosting tarafında `data/` ve `app/` klasörleri **web root'un dışında**, `public_html/` içeriği ise web root'un **içinde** olmalı — aksi halde `data/site.json` ve `data/admin.json` doğrudan URL ile erişilebilir hale gelir.

```
domains/crcvinc.com/
├── app/                  ← buraya yükle (public_html'in YANINA, içine değil)
├── data/                 ← buraya yükle
└── public_html/          ← bu klasörün İÇERİĞİNİ public_html'e yükle
    ├── index.php
    ├── .htaccess
    ├── admin/
    └── assets/
```

FTP ile:
1. `app/` klasörünü `domains/crcvinc.com/app/` olarak yükle.
2. `data/` klasörünü `domains/crcvinc.com/data/` olarak yükle (`site.json` dahil, `admin.json` henüz yok — panelden oluşacak).
3. `public_html/` klasörünün **içeriğini** (kendisini değil) `domains/crcvinc.com/public_html/` içine yükle.

## Kontrol

- `data/` klasörüne PHP'nin yazma izni olmalı (chmod 755, dosyalar 644) — panel `site.json`'u ve ilk kurulumda `admin.json`'u buraya yazıyor.
- `.htaccess` dosyası yüklendiğinden emin ol (bazı FTP istemcileri gizli/nokta ile başlayan dosyaları göstermez — "gizli dosyaları göster" seçeneğini aç).
- PHP sürümü: DirectAdmin panelinden (Select PHP Version) en az PHP 8.1 seçili olmalı.

## İlk açılış

1. `https://www.crcvinc.com/admin/` adresine git → kurulum ekranı otomatik açılır.
2. Kalıcı kullanıcı adı/şifre oluştur.
3. "Genel & Hero" sekmesinden hero YouTube linkini, telefon/WhatsApp/e-posta bilgilerini gir.
4. Diğer sekmelerden (Menü, Sayaçlar, Hizmetler, Makine Parkı, Projeler, Referanslar, Sayfa Metinleri) içerikleri doldur.

## E-posta (iletişim formu)

`app/contact.php` PHP'nin yerleşik `mail()` fonksiyonunu kullanır — DirectAdmin'de ek ayar gerekmez.
Gönderim başarısız olsa bile mesajlar `data/messages.json` içine kaydedilir, hiçbir mesaj kaybolmaz.

## Alan adı / DNS

crcvinc.com.tr için DNS/Cloudflare yapılandırması ayrı yürütülüyor (bkz. [info.md](../info.md)) — bu belge yalnızca uygulamanın dosya deploy'unu kapsar.
