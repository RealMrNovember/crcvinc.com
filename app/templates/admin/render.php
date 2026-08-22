<?php
declare(strict_types=1);

/** Tek dosyalık admin paneli görünümü: sekmeli formlar, her sekme kendi POST'unu tab=... ile ayırt eder. */
function renderAdminPanel(array $site, bool $saved, ?string $error): void
{
    $settings = $site['settings'];
    $activeTab = $_GET['tab'] ?? 'settings';
    $tabs = [
        'settings' => 'Genel & Hero',
        'menu' => 'Menü',
        'counters' => 'Sayaçlar',
        'services' => 'Hizmetler',
        'fleet' => 'Makine Parkı',
        'projects' => 'Projeler',
        'clients' => 'Referanslar',
        'texts' => 'Bölüm Başlıkları',
        'pages' => 'Sayfa Metinleri',
        'account' => 'Hesap',
    ];
    if (!isset($tabs[$activeTab])) {
        $activeTab = 'settings';
    }
    ?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Yönetim Paneli — CRC Vinç</title>
<link rel="stylesheet" href="<?= e(assetUrl('/assets/css/admin.css')) ?>">
</head>
<body class="admin-body">
<header class="admin-topbar">
  <span class="admin-brand">CRC Vinç · Yönetim Paneli</span>
  <div class="admin-topbar-actions">
    <a href="/" target="_blank" rel="noopener">Siteyi Görüntüle ↗</a>
    <a href="/admin/logout.php">Çıkış Yap</a>
    <a class="admin-credit" href="https://cicibyte.com" target="_blank" rel="noopener">CiciByte Teknoloji</a>
  </div>
</header>
<div class="admin-shell">
  <nav class="admin-tabs">
    <?php foreach ($tabs as $key => $label): ?>
    <a href="?tab=<?= e($key) ?>" class="<?= $key === $activeTab ? 'is-active' : '' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <main class="admin-content">
    <?php if ($saved): ?><div class="admin-notice admin-notice-ok">Değişiklikler kaydedildi.</div><?php endif; ?>
    <?php if ($error): ?><div class="admin-notice admin-notice-error"><?= e($error) ?></div><?php endif; ?>

    <?php if ($activeTab === 'settings'): ?>
      <?php renderSettingsTab($settings); ?>
    <?php elseif ($activeTab === 'menu'): ?>
      <?php renderMenuTab($site['menu']); ?>
    <?php elseif ($activeTab === 'counters'): ?>
      <?php renderCountersTab($site['counters']); ?>
    <?php elseif ($activeTab === 'services'): ?>
      <?php renderServicesTab($site['services']); ?>
    <?php elseif ($activeTab === 'fleet'): ?>
      <?php renderFleetTab($site['fleet']); ?>
    <?php elseif ($activeTab === 'projects'): ?>
      <?php renderProjectsTab($site['projects']); ?>
    <?php elseif ($activeTab === 'clients'): ?>
      <?php renderClientsTab($site['clients']); ?>
    <?php elseif ($activeTab === 'texts'): ?>
      <?php renderTextsTab($site); ?>
    <?php elseif ($activeTab === 'pages'): ?>
      <?php renderPagesTab($site['pages']); ?>
    <?php elseif ($activeTab === 'account'): ?>
      <?php renderAccountTab(); ?>
    <?php endif; ?>
  </main>
</div>
</body>
</html>
<?php
}

function renderSettingsTab(array $s): void
{
    ?>
    <h2>Genel Bilgiler &amp; Hero</h2>
    <p class="admin-hint">Hero alanındaki YouTube videosunu buradan değiştirebilirsiniz — tam link (ör. https://youtu.be/...) veya sadece video ID'sini yapıştırmanız yeterli.</p>
    <form method="post" enctype="multipart/form-data">
      <?= csrfField() ?>
      <input type="hidden" name="tab" value="settings">
      <fieldset>
        <legend>Logo</legend>
        <p class="admin-hint">Yeni bir logo yükleyin (SVG, PNG, JPG veya WEBP — en fazla 2MB). Koyu zeminli site üzerinde okunaklı olması için açık renkli/beyaz bir logo tercih edin.</p>
        <div class="admin-logo-preview">
          <img src="<?= e(assetUrl(brandLogoUrl($s))) ?>" alt="Mevcut logo" class="admin-logo-current">
        </div>
        <label>Yeni logo dosyası<input type="file" name="logo_file" accept=".svg,.png,.jpg,.jpeg,.webp"></label>
        <?php if (!empty($s['logo_path'])): ?>
        <label class="admin-checkbox-inline"><input type="checkbox" name="remove_logo" value="1"> Özel logoyu kaldır, varsayılan CRC amblemine dön</label>
        <?php endif; ?>
      </fieldset>
      <fieldset>
        <legend>Yükleme Ekranı (Preloader)</legend>
        <p class="admin-hint">Site açılırken kısa süreliğine görünen vinç animasyonu.</p>
        <label class="admin-checkbox-inline"><input type="checkbox" name="preloader_enabled" value="1" <?= !empty($s['preloader_enabled']) ? 'checked' : '' ?>> Yükleme ekranını göster</label>
        <div class="admin-row">
          <label>Gösterim süresi (ms, en fazla 3000)<input type="number" name="preloader_duration" min="0" max="3000" step="100" value="<?= e((string) ($s['preloader_duration'] ?? 400)) ?>"></label>
          <label>Alt yazı<input type="text" name="preloader_text" value="<?= e($s['preloader_text'] ?? '') ?>"></label>
        </div>
        <button type="submit" name="preloader_action" value="reset" class="admin-save admin-save-secondary" formnovalidate>Yükleme Ekranını Varsayılana Sıfırla</button>
      </fieldset>
      <fieldset>
        <legend>Hero Videosu</legend>
        <label>YouTube video linki veya ID<input type="text" name="hero_video_id" value="<?= e($s['hero_video_id']) ?>" placeholder="https://youtu.be/XXXXXXXXXXX"></label>
      </fieldset>
      <fieldset>
        <legend>Hero Metinleri</legend>
        <label>Başlık<input type="text" name="hero_title" value="<?= e($s['hero_title']) ?>"></label>
        <label>Alt başlık<textarea name="hero_subtitle" rows="2"><?= e($s['hero_subtitle']) ?></textarea></label>
        <div class="admin-row">
          <label>Birincil buton metni<input type="text" name="hero_cta_primary" value="<?= e($s['hero_cta_primary']) ?>"></label>
          <label>İkincil buton metni<input type="text" name="hero_cta_secondary" value="<?= e($s['hero_cta_secondary']) ?>"></label>
        </div>
      </fieldset>
      <fieldset>
        <legend>Firma &amp; İletişim</legend>
        <div class="admin-row">
          <label>Site adı<input type="text" name="site_name" value="<?= e($s['site_name']) ?>"></label>
          <label>Slogan<input type="text" name="tagline" value="<?= e($s['tagline']) ?>"></label>
        </div>
        <label>Resmi unvan<input type="text" name="legal_name" value="<?= e($s['legal_name']) ?>"></label>
        <div class="admin-row">
          <label>Telefon (tel: linki için, ör. +905000000000)<input type="text" name="phone" value="<?= e($s['phone']) ?>"></label>
          <label>Telefon (görünen)<input type="text" name="phone_display" value="<?= e($s['phone_display']) ?>"></label>
        </div>
        <div class="admin-row">
          <label>WhatsApp numarası (ülke koduyla, + işaretsiz)<input type="text" name="whatsapp" value="<?= e($s['whatsapp']) ?>"></label>
          <label>E-posta<input type="email" name="email" value="<?= e($s['email']) ?>"></label>
        </div>
        <label>Adres<input type="text" name="address" value="<?= e($s['address']) ?>"></label>
        <div class="admin-row">
          <label>Instagram linki<input type="url" name="instagram" value="<?= e($s['instagram']) ?>"></label>
          <label>LinkedIn linki<input type="url" name="linkedin" value="<?= e($s['linkedin']) ?>"></label>
        </div>
        <label>Google Harita gömme kodu (isteğe bağlı, iframe)<textarea name="map_embed" rows="2"><?= e($s['map_embed']) ?></textarea></label>
        <label>Footer açıklama metni<textarea name="footer_text" rows="2"><?= e($s['footer_text']) ?></textarea></label>
      </fieldset>
      <button type="submit" class="admin-save">Kaydet</button>
    </form>
    <?php
}

function renderMenuTab(array $menu): void
{
    ?>
    <h2>Menü</h2>
    <p class="admin-hint">Boş bırakılan satırlar kaydedilirken silinir.</p>
    <form method="post">
      <?= csrfField() ?>
      <input type="hidden" name="tab" value="menu">
      <?php foreach (repeatRows($menu, 8) as $item): ?>
      <div class="admin-row">
        <label>Başlık<input type="text" name="menu_label[]" value="<?= e($item['label'] ?? '') ?>"></label>
        <label>Link (ör. /hizmetler)<input type="text" name="menu_url[]" value="<?= e($item['url'] ?? '') ?>"></label>
      </div>
      <?php endforeach; ?>
      <button type="submit" class="admin-save">Kaydet</button>
    </form>
    <?php
}

function renderCountersTab(array $counters): void
{
    ?>
    <h2>Sayaçlar</h2>
    <form method="post">
      <?= csrfField() ?>
      <input type="hidden" name="tab" value="counters">
      <?php foreach (repeatRows($counters, 6) as $item): ?>
      <div class="admin-row admin-row-3">
        <label>Sayı<input type="number" name="counter_value[]" value="<?= e((string) ($item['value'] ?? '')) ?>"></label>
        <label>Ek (ör. +, Ton)<input type="text" name="counter_suffix[]" value="<?= e($item['suffix'] ?? '') ?>"></label>
        <label>Etiket<input type="text" name="counter_label[]" value="<?= e($item['label'] ?? '') ?>"></label>
      </div>
      <?php endforeach; ?>
      <button type="submit" class="admin-save">Kaydet</button>
    </form>
    <?php
}

function renderServicesTab(array $services): void
{
    ?>
    <h2>Hizmetler</h2>
    <form method="post">
      <?= csrfField() ?>
      <input type="hidden" name="tab" value="services">
      <?php foreach (repeatRows($services, 8) as $item): ?>
      <div class="admin-card-row">
        <div class="admin-row">
          <label>İkon
            <select name="service_icon[]">
              <?php foreach (['crane' => 'Vinç', 'basket' => 'Sepetli Platform', 'truck' => 'Kamyon', 'gear' => 'Dişli'] as $val => $label): ?>
              <option value="<?= $val ?>" <?= ($item['icon'] ?? 'crane') === $val ? 'selected' : '' ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Başlık<input type="text" name="service_title[]" value="<?= e($item['title'] ?? '') ?>"></label>
        </div>
        <label>Açıklama<textarea name="service_desc[]" rows="2"><?= e($item['desc'] ?? '') ?></textarea></label>
      </div>
      <?php endforeach; ?>
      <button type="submit" class="admin-save">Kaydet</button>
    </form>
    <?php
}

function renderFleetTab(array $fleet): void
{
    ?>
    <h2>Makine Parkı</h2>
    <p class="admin-hint">Görsel alanına, medya yöneticinize yüklediğiniz görselin adres (URL) yolunu yazın — ör. /assets/img/mobil-vinc.jpg</p>
    <form method="post">
      <?= csrfField() ?>
      <input type="hidden" name="tab" value="fleet">
      <?php foreach (repeatRows($fleet, 8) as $item): ?>
      <div class="admin-card-row">
        <div class="admin-row">
          <label>Başlık<input type="text" name="fleet_title[]" value="<?= e($item['title'] ?? '') ?>"></label>
          <label>Kapasite (ör. 25–500 ton)<input type="text" name="fleet_capacity[]" value="<?= e($item['capacity'] ?? '') ?>"></label>
        </div>
        <label>Açıklama<textarea name="fleet_desc[]" rows="2"><?= e($item['desc'] ?? '') ?></textarea></label>
        <label>Görsel URL<input type="text" name="fleet_image[]" value="<?= e($item['image'] ?? '') ?>"></label>
      </div>
      <?php endforeach; ?>
      <button type="submit" class="admin-save">Kaydet</button>
    </form>
    <?php
}

function renderProjectsTab(array $projects): void
{
    ?>
    <h2>Projeler</h2>
    <form method="post">
      <?= csrfField() ?>
      <input type="hidden" name="tab" value="projects">
      <?php foreach (repeatRows($projects, 9) as $item): ?>
      <div class="admin-card-row">
        <div class="admin-row">
          <label>Başlık<input type="text" name="project_title[]" value="<?= e($item['title'] ?? '') ?>"></label>
          <label>Müşteri<input type="text" name="project_client[]" value="<?= e($item['client'] ?? '') ?>"></label>
        </div>
        <div class="admin-row">
          <label>Rakam (ör. 120 ton)<input type="text" name="project_stat[]" value="<?= e($item['stat'] ?? '') ?>"></label>
          <label>Görsel URL<input type="text" name="project_image[]" value="<?= e($item['image'] ?? '') ?>"></label>
        </div>
        <label>Açıklama<textarea name="project_desc[]" rows="2"><?= e($item['desc'] ?? '') ?></textarea></label>
      </div>
      <?php endforeach; ?>
      <button type="submit" class="admin-save">Kaydet</button>
    </form>
    <?php
}

function renderClientsTab(array $clients): void
{
    ?>
    <h2>Referans Logoları / İsimleri</h2>
    <p class="admin-hint">Her satıra bir referans adı yazın.</p>
    <form method="post">
      <?= csrfField() ?>
      <input type="hidden" name="tab" value="clients">
      <textarea name="clients_raw" rows="10" class="admin-textarea-wide"><?= e(implode("\n", $clients)) ?></textarea>
      <button type="submit" class="admin-save">Kaydet</button>
    </form>
    <?php
}

function renderTextsTab(array $site): void
{
    $home = $site['home'];
    $contact = $site['contact'];
    $founder = $site['founder'] ?? [];
    $safety = repeatRows($site['safety'], 4);
    $hours = repeatRows($site['hours'] ?? [], 7);
    ?>
    <h2>Bölüm Başlıkları</h2>
    <p class="admin-hint">Ana sayfadaki bölüm başlıkları, güvenlik şeridi ve iletişim sayfası metinlerini buradan değiştirebilirsiniz.</p>
    <form method="post">
      <?= csrfField() ?>
      <input type="hidden" name="tab" value="texts">

      <fieldset>
        <legend>Hero Üst Etiketi</legend>
        <label>Hero'da başlığın üstündeki küçük yazı<input type="text" name="hero_kicker" value="<?= e($site['settings']['hero_kicker'] ?? '') ?>"></label>
      </fieldset>

      <fieldset>
        <legend>Ana Sayfa Bölüm Başlıkları</legend>
        <div class="admin-row">
          <label>Hizmetler — üst etiket<input type="text" name="home_services_kicker" value="<?= e($home['services_kicker']) ?>"></label>
          <label>Hizmetler — başlık<input type="text" name="home_services_title" value="<?= e($home['services_title']) ?>"></label>
        </div>
        <div class="admin-row">
          <label>Makine Parkı — üst etiket<input type="text" name="home_fleet_kicker" value="<?= e($home['fleet_kicker']) ?>"></label>
          <label>Makine Parkı — başlık<input type="text" name="home_fleet_title" value="<?= e($home['fleet_title']) ?>"></label>
        </div>
        <div class="admin-row">
          <label>Projeler — üst etiket<input type="text" name="home_projects_kicker" value="<?= e($home['projects_kicker']) ?>"></label>
          <label>Projeler — başlık<input type="text" name="home_projects_title" value="<?= e($home['projects_title']) ?>"></label>
        </div>
        <div class="admin-row">
          <label>Teklif çağrısı — başlık<input type="text" name="home_cta_title" value="<?= e($home['cta_title']) ?>"></label>
          <label>Teklif çağrısı — alt yazı<input type="text" name="home_cta_subtitle" value="<?= e($home['cta_subtitle']) ?>"></label>
        </div>
      </fieldset>

      <fieldset>
        <legend>Güvenlik / İSG Şeridi</legend>
        <p class="admin-hint">Boş bırakılan satırlar kaydedilirken silinir.</p>
        <?php foreach ($safety as $item): ?>
        <div class="admin-row">
          <label>Başlık<input type="text" name="safety_title[]" value="<?= e($item['title'] ?? '') ?>"></label>
          <label>Açıklama<input type="text" name="safety_desc[]" value="<?= e($item['desc'] ?? '') ?>"></label>
        </div>
        <?php endforeach; ?>
      </fieldset>

      <fieldset>
        <legend>İletişim Sayfası Metinleri</legend>
        <div class="admin-row">
          <label>Başlık<input type="text" name="contact_hero_title" value="<?= e($contact['hero_title']) ?>"></label>
          <label>Alt yazı<input type="text" name="contact_hero_subtitle" value="<?= e($contact['hero_subtitle']) ?>"></label>
        </div>
        <div class="admin-row">
          <label>İletişim bilgisi kutusu başlığı<input type="text" name="contact_info_title" value="<?= e($contact['info_title']) ?>"></label>
          <label>Form kutusu başlığı<input type="text" name="contact_form_title" value="<?= e($contact['form_title']) ?>"></label>
        </div>
      </fieldset>

      <fieldset>
        <legend>Kurucu</legend>
        <p class="admin-hint">Kurumsal sayfasında görünür. Boş bırakılırsa kurucu kartı gösterilmez.</p>
        <div class="admin-row">
          <label>Ad Soyad<input type="text" name="founder_name" value="<?= e($founder['name'] ?? '') ?>"></label>
          <label>Unvan<input type="text" name="founder_title" value="<?= e($founder['title'] ?? '') ?>"></label>
        </div>
      </fieldset>

      <fieldset>
        <legend>Çalışma Saatleri</legend>
        <p class="admin-hint">İletişim sayfasında listelenir. Boş bırakılan satırlar kaydedilirken silinir (örn. kapalı gün için "Kapalı" yazın).</p>
        <?php foreach ($hours as $item): ?>
        <div class="admin-row">
          <label>Gün<input type="text" name="hours_day[]" value="<?= e($item['day'] ?? '') ?>"></label>
          <label>Saat<input type="text" name="hours_value[]" value="<?= e($item['value'] ?? '') ?>"></label>
        </div>
        <?php endforeach; ?>
      </fieldset>

      <button type="submit" class="admin-save">Kaydet</button>
    </form>
    <?php
}

function renderAccountTab(): void
{
    ?>
    <h2>Hesap Bilgileri</h2>
    <p class="admin-hint">Panel kullanıcı adınızı ve şifrenizi buradan değiştirebilirsiniz. Şifreyi değiştirmek istemiyorsanız o alanları boş bırakın.</p>
    <form method="post" class="admin-account-form">
      <?= csrfField() ?>
      <input type="hidden" name="tab" value="account">
      <label>Mevcut şifre*<input type="password" name="current_password" required></label>
      <label>Yeni kullanıcı adı<input type="text" name="new_username" value="<?= e(currentAdminUsername()) ?>" required></label>
      <div class="admin-row">
        <label>Yeni şifre <span class="admin-hint-inline">(değiştirmek istemiyorsanız boş bırakın)</span><input type="password" name="new_password" minlength="8"></label>
        <label>Yeni şifre (tekrar)<input type="password" name="new_password_confirm" minlength="8"></label>
      </div>
      <button type="submit" class="admin-save">Kaydet</button>
    </form>
    <?php
}

function renderPagesTab(array $pages): void
{
    ?>
    <h2>Sayfa Metinleri</h2>
    <form method="post">
      <?= csrfField() ?>
      <input type="hidden" name="tab" value="pages">
      <?php foreach ($pages as $slug => $page): ?>
      <fieldset>
        <legend><?= e($page['title'] ?? $slug) ?> (/<?= e($slug) ?>)</legend>
        <label>Başlık<input type="text" name="page_title_<?= e($slug) ?>" value="<?= e($page['title'] ?? '') ?>"></label>
        <label>Metin<textarea name="page_body_<?= e($slug) ?>" rows="5"><?= e($page['body'] ?? '') ?></textarea></label>
      </fieldset>
      <?php endforeach; ?>
      <button type="submit" class="admin-save">Kaydet</button>
    </form>
    <?php
}

/** Liste + boş satırlar döndürür ki formda her zaman birkaç ek boş kayıt alanı görünsün. */
function repeatRows(array $items, int $minTotal): array
{
    $items = array_values($items);
    while (count($items) < $minTotal) {
        $items[] = [];
    }
    return $items;
}
