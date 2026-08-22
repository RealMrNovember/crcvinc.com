# CRC Vinç — Piyasa Araştırması ve Site Konsepti

*Tarih: 22 Ağustos 2026 — Site geliştirme öncesi araştırma raporu*

---

## 1. Firma Kimliği: CRC Vinç

Kamuya açık dijital ayak izi **neredeyse sıfır**. Tek doğrulanmış kayıt, crcvinc.com.tr WHOIS/TRABİS kaydındaki resmi unvan:

> **CRC VİNÇ KİRALAMA YEDEK PARÇA İTHALAT İHRACAT SANAYİ TİCARET LİMİTED ŞİRKETİ** (İstanbul, Ltd. Şti.)

| Bulgu | Durum |
|---|---|
| Faaliyet alanı | Vinç kiralama + yedek parça + ithalat/ihracat (unvandan; mobil/kule vinç ayrımı doğrulanamadı) |
| Alan adları | crcvinc.com ve crcvinc.com.tr — ikisi de **24.04.2023** tescilli (IHS Telekom), NS'ler Cloudflare |
| Web sitesi | Yok — crcvinc.com'da DirectAdmin park sayfası |
| Sosyal medya | Instagram / Facebook / LinkedIn hesabı **bulunamadı** |
| Google Maps kaydı | **Bulunamadı** |
| Telefon / adres / filo bilgisi | **Bulunamadı** — tümü firmadan alınmalı |

**Karışma riski:** CSR Vinç Kiralama (İzmir), CRS Vinç (crsvinc.com.tr) ve CRC Industries (kimya) benzer isimli, ilgisiz firmalar. SEO'da marka ayrıştırması (tam unvan, LocalBusiness schema, Google Business Profile) önemli.

### ⚠️ Acil teknik sorun: crcvinc.com.tr DNS'i bozuk
.tr delegasyonundaki NS sunucuları sorguları REFUSED ile reddediyor (lame delegation) → alan adı **SERVFAIL** veriyor, hiç açılmıyor. Cloudflare'da zone kurulup TRABİS'teki NS kayıtlarıyla eşitlenmeli. Yayına almadan önce düzeltilecek.

---

## 2. Referans Site Analizi: ale.com.kz

Müşterinin beğendiği site, eski Mammoet iştiraki **ALE Kazakhstan** (Atyrau merkezli ağır kaldırma/gabari dışı taşıma firması; TengizChevrOil, NCOC, SOCAR referanslı).

**Asıl bulgu:** Site **Tilda** (no-code builder, BDT'de yaygın) ile yapılmış. GSAP yok, Swiper yok, parallax yok, sayaç yok — **hero'da video bile yok**. Müşterinin sevdiği "efekt hissi" tamamen şunlardan ibaret:

- 22 adet `fadeinup` scroll-reveal animasyonu (Tilda'nın hazır animasyonu)
- Kartlarda hover mikro-animasyonları (opacity, kaydırma, 45° ok dönmesi)
- Partner logolarında basit bir slider

**Sonuç:** Bu hissi modern araçlarla (GSAP/AOS + Swiper + gerçek video hero) hem çok daha kaliteli hem çok daha performanslı vermek kolay. Çıta düşük.

**Ale'den alınacak iyi fikirler:** temiz bölüm akışı (hizmet → proje kanıtı → coğrafya → partner logoları → form), her sayfada dönüşüm odaklı form, sade tek seviye menü.

**Ale'nin yapmadığı, bizim yapacaklarımız:** hero'da tam ekran sessiz döngü video, animasyonlu sayaçlar, dolu proje detay sayfaları (galeri + rakamlar), bölüm başına çeşitlenen reveal animasyonları, `prefers-reduced-motion` desteği.

---

## 3. Sektör Analizi

### Uluslararası liderler
- **Mammoet** — statik güçlü hero + tek iddialı slogan; navigasyon hizmet değil **sektör odaklı** (Energy, Renewables, Infrastructure...); vaka analizleri (case studies) içerik omurgası; "Safety first" ayrı bölüm. (ALE'yi 2020'de satın aldı, marka kapandı.)
- **Sarens** — "Nothing too Heavy, Nothing too High"; tip bazlı filo sayfaları. Ortaklığı **Sarens Nass** sitesi birebir uygulanabilir şablon: hero → **sayaçlar (proje, kazasız iş saati, yıl, çalışan)** → hizmet kartları → filo kategorileri → sektör ikonları → müşteri logoları (Aramco, Samsung...) → iletişim CTA + sertifika logoları.
- **Liebherr** — kart-grid ürün katalogu; filo sayfası için uyarlanabilir model.

### Türkiye (6 site incelendi)
- **Hareket** (sektör lideri, en iyi yerli benchmark): koyu tema, proje odaklı slider, menüde Sektörler + Ekipmanlar + SEÇ/Sertifikalar, 4 dil (TR/EN/RU/FR), projelerde somut tonajlar ("2.150 T, 3 STS vinç").
- **Vinç Deposu** (SEO/dönüşüm makinesi): sayaçlar, **tonaj × süre fiyat tabloları**, 6 adımlı kiralama süreci, İSG bölümü, 18 soruluk SSS, **39 ilçe için ayrı SEO sayfası**, tüm CTA'lar `tel:` + WhatsApp.
- **Fora / Kardeşler / AVK / Kıroğlu / Coşkun**: statik hero veya 3'lü slider, hizmet kartları, ilçe bazlı bölge sayfaları, sigorta/poliçe bölümü, makine parkı listeleri ("500 t / 136 m"), sabit WhatsApp butonu, yoğun "7/24" vurgusu.

### Sektör kalıpları (sentez)

**Şart sayfalar:** Ana Sayfa • Hizmetler (tip bazlı alt sayfalar) • Makine Parkı/Filo • Projeler/Referanslar • Hakkımızda • İletişim/Teklif. TR'ye özgü ek: **Hizmet Bölgeleri** (il/ilçe SEO sayfaları), SSS/Blog. Prestij katmanı: **Sektörler** ve **Sertifikalar/İSG** sayfaları.

**Evrensel efekt öğeleri:** istatistik sayaçları (yıl, proje, filo, kazasız saat), kart-grid'ler (4–8 hizmet, 6–8 filo kategorisi), müşteri logo şeridi (10–30 logo).

**TR'ye özgü dönüşüm blokları:** sabit WhatsApp (wa.me) butonu + "Hemen Ara" `tel:` CTA, header'da 7/24 + telefon, tonaj/kapasite tabloları, sigorta + sertifikalı operatör + İSG bölümü.

**Renk dili:** koyu zemin + vinç sarısı/turuncu vurgu (ağır sanayi prestiji, Hareket tarzı) veya mavi/beyaz kurumsal.

---

## 4. Önerilen Konsept: CRC Vinç Sitesi

**Konumlanma:** TR yerel firmalarının dönüşüm blokları + Hareket/Sarens'in kurumsal prestij dili + müşterinin istediği "cancanlı" efekt katmanının kaliteli versiyonu.

### Site haritası
```
Ana Sayfa
Hizmetler        → mobil vinç, sepetli platform, proje taşımacılığı... (alt sayfalar)
Makine Parkı     → kategori kartları → model + tonaj/bom detayı
Projeler         → galeri + vaka anlatımı (tonaj rakamlarıyla)
Kurumsal         → Hakkımızda, Sertifikalar/İSG
İletişim         → teklif formu + harita
(faz 2: Sektörler, Hizmet Bölgeleri, Blog/SSS — SEO büyümesi)
```

### Ana sayfa akışı
1. **Hero: tam ekran YouTube videosu** (sessiz, döngü, overlay + kademeli başlık girişi) — *müşterinin ana isteği; video linki panelden değiştirilebilir*
2. Animasyonlu **sayaçlar** (yıl, proje, filo, tonaj)
3. **Hizmet kartları** (hover mikro-animasyonlu)
4. **Filo kategorileri** grid
5. **Projeler/referanslar** — Swiper slider
6. Müşteri **logo şeridi** (marquee)
7. **İSG/sertifika** şeridi
8. **Teklif CTA** + iletişim formu
9. Sabit **WhatsApp** butonu + header'da 7/24 telefon

### Efekt dili (ale.com.kz hissinin kaliteli hali)
- GSAP ScrollTrigger (veya AOS) ile bölüm başına çeşitlenen reveal'lar (fade-up, clip-path, stagger)
- CountUp sayaçlar, Swiper karuselleri, hafif parallax + kart hover yükselmesi
- `prefers-reduced-motion` desteği, mobilde de 60fps

---

## 5. Teknik Mimari ve Backend Önerisi

**Kısıt:** Kebirhost DirectAdmin paylaşımlı hosting → PHP + MySQL ortamı. Node/SSR yok.

**İhtiyaç:** Müşteri kendi başına şunları değiştirebilsin: hero YouTube linki, menü, sayfa içerikleri, (muhtemelen) proje/makine ekleme. Geliştirici sürekli revize yapmak istemiyor.

### Öneri: Özel hafif PHP admin paneli (önerilen ✅)
- Frontend: statik-hızlı PHP şablonları + Tailwind + GSAP/AOS + Swiper
- İçerik: JSON dosyaları veya birkaç MySQL tablosu (ayarlar, menü, sayfalar, projeler, makineler)
- Admin: tek şifreli giriş, sade form ekranları — "Hero video linki: [____] Kaydet"
- Artıları: sıfır güncelleme/bakım yükü, saldırı yüzeyi minimal, tam tasarım özgürlüğü (WP tema kısıtı yok), müşteri için kafa karıştırmayan 3-5 ekranlık panel
- Eksileri: panel ekranlarını bizim yazmamız gerekir (kapsam dar olduğu için makul)

### Alternatif: WordPress + özel tema
- Artıları: hazır admin, medya yönetimi, müşteri alışkanlığı
- Eksileri: sürekli çekirdek/plugin güvenlik güncellemesi, paylaşımlı hostingde hedef tahtası, efektli özel tasarım için tema geliştirmek neredeyse aynı efor, panel karmaşası "basit backend" isteğiyle çelişiyor

Karar: **özel hafif panel**. Müşterinin isteği tam olarak "az ekranlı, bozamayacağı" bir panel; WP bu iş için fazla ve bakım yükü bize kalır.

---

## 6. Yapılacaklar / Müşteriden İstenecekler

### Teknik (bizde)
- [x] ~~crcvinc.com.tr DNS~~ — ayrı sunucu oturumunda çözülüyor: CF zone kurulu, İHS panelinden NS değişikliği kullanıcıda. Bu oturum site geliştirmeye odaklı.
- [ ] FTP şifresi gelince hosting ortamı keşfi (PHP sürümü, MySQL, SSL)
- [ ] Repo iskeleti + tasarım sistemi (renk: koyu + vinç sarısı önerisi)

### İçerik (müşteriden)
- [ ] Hero YouTube video linki
- [ ] Telefon, WhatsApp hattı, adres, e-posta (kurumsal e-postalar mail.crcvinc.com'da açılacak)
- [ ] Makine parkı listesi (model, tonaj, bom boyu, fotoğraflar)
- [ ] Referans projeler + müşteri logoları (izinli)
- [ ] Sertifikalar, sigorta poliçeleri, operatör belgeleri
- [ ] Firma tanıtım metni / kuruluş hikayesi
- [ ] Logo (vektörel) ve kurumsal renkler varsa

### SEO / görünürlük (yayınla birlikte)
- [ ] Google Business Profile kaydı (harita kaydı hiç yok!)
- [ ] LocalBusiness schema + tam unvan (CSR/CRS Vinç karışmasına karşı)
- [ ] Instagram/LinkedIn hesaplarının açılması önerisi
