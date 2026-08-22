# crcvinc.com

CRC Vinç kurumsal web sitesi — özel hafif PHP altyapısı (framework yok, veritabanı yok).

## Yapı

```
public_html/        ← web root (hosting'de domains/crcvinc.com/public_html)
  index.php         ← front controller (tüm sayfalar)
  admin/            ← yönetim paneli
  assets/           ← css / js / görseller
app/                ← şablonlar ve yardımcılar (web root DIŞINA yüklenir)
data/               ← site.json (tüm içerik) — web root DIŞINA yüklenir
router.php          ← sadece lokal geliştirme için
```

## Lokal geliştirme

```bash
php -S localhost:8080 router.php
```

## Deploy (DirectAdmin / FTP)

`app/` ve `data/` klasörlerini `domains/crcvinc.com/` altına (public_html'in **yanına**),
`public_html/` içeriğini `domains/crcvinc.com/public_html/` içine yükle.
Ayrıntı: [docs/deploy.md](docs/deploy.md)

## Yönetim paneli

`/admin/` — ilk açılışta panel şifresi oluşturulur (`data/admin.json`, repoya girmez).
Müşteri panelden şunları yönetir: hero YouTube videosu, menü, iletişim bilgileri,
sayaçlar, hizmetler, makine parkı, projeler, sayfa metinleri.
