<?php
declare(strict_types=1);

const LOGO_MAX_BYTES = 2 * 1024 * 1024;
const GALLERY_MAX_BYTES = 5 * 1024 * 1024;
const IMAGE_ALLOWED = [
    'image/svg+xml' => 'svg',
    'image/png' => 'png',
    'image/jpeg' => 'jpg',
    'image/webp' => 'webp',
];

require dirname(__DIR__, 2) . '/app/bootstrap.php';
require APP_DIR . '/auth.php';

if (!adminCredentialsExist()) {
    header('Location: /admin/setup.php');
    exit;
}

requireAdminLogin();

$site = siteContent();
$saved = false;
$error = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!checkCsrf()) {
        $error = 'Oturum süresi doldu, lütfen formu tekrar gönderin.';
    } else {
        $tab = $_POST['tab'] ?? 'settings';

        // Hesap sekmesi site.json'u değil admin.json'u değiştirir, ayrı akış.
        if ($tab === 'account') {
            $newPassword = (string) ($_POST['new_password'] ?? '');
            $newPasswordConfirm = (string) ($_POST['new_password_confirm'] ?? '');
            if ($newPassword !== '' && $newPassword !== $newPasswordConfirm) {
                $error = 'Yeni şifreler eşleşmiyor.';
            } else {
                [$ok, $message] = changeAdminCredentials(
                    (string) ($_POST['current_password'] ?? ''),
                    (string) ($_POST['new_username'] ?? ''),
                    $newPassword !== '' ? $newPassword : null
                );
                if ($ok) {
                    $_SESSION['admin_user'] = (string) ($_POST['new_username'] ?? '');
                    $saved = true;
                } else {
                    $error = $message;
                }
            }
        } else {
            try {
                switch ($tab) {
                    case 'settings':
                        $site['settings'] = array_merge($site['settings'], sanitizeSettings($_POST));
                        if (($_POST['preloader_action'] ?? '') === 'reset') {
                            $site['settings']['preloader_enabled'] = true;
                            $site['settings']['preloader_duration'] = 400;
                            $site['settings']['preloader_text'] = 'CRC Vinç';
                        }
                        if (!empty($_POST['remove_logo'])) {
                            removeCustomLogo();
                            $site['settings']['logo_path'] = '';
                        } elseif (!empty($_FILES['logo_file']['name'])) {
                            $uploaded = handleLogoUpload($_FILES['logo_file']);
                            if ($uploaded === null) {
                                $error = 'Logo yüklenemedi: dosya türü desteklenmiyor veya boyutu 2MB üzerinde. İzin verilen türler: SVG, PNG, JPG, WEBP.';
                            } else {
                                $site['settings']['logo_path'] = $uploaded;
                            }
                        }
                        break;
                    case 'menu':
                        $site['menu'] = sanitizeMenu($_POST);
                        break;
                    case 'counters':
                        $site['counters'] = sanitizeCounters($_POST);
                        break;
                    case 'services':
                        $site['services'] = sanitizeServices($_POST);
                        break;
                    case 'fleet':
                        $site['fleet'] = sanitizeFleet($_POST, $_FILES);
                        break;
                    case 'projects':
                        $site['projects'] = sanitizeProjects($_POST, $_FILES);
                        break;
                    case 'clients':
                        $site['clients'] = sanitizeClients($_POST, $_FILES);
                        break;
                    case 'blog':
                        $site['blog']['page_title'] = trim((string) ($_POST['blog_page_title'] ?? 'Blog'));
                        $site['blog']['page_intro'] = trim((string) ($_POST['blog_page_intro'] ?? ''));
                        $site['blog']['posts'] = sanitizeBlog($_POST, $_FILES);
                        break;
                    case 'seo':
                        $site['settings']['seo_default_description'] = trim((string) ($_POST['seo_default_description'] ?? ''));
                        $site['settings']['seo_keywords'] = trim((string) ($_POST['seo_keywords'] ?? ''));
                        $site['settings']['seo_gsc_verification'] = trim((string) ($_POST['seo_gsc_verification'] ?? ''));
                        $site['settings']['seo_ga_id'] = trim((string) ($_POST['seo_ga_id'] ?? ''));
                        if (!empty($_POST['remove_og_image'])) {
                            $site['settings']['seo_og_image'] = '';
                        } elseif (!empty($_FILES['seo_og_image_file']['name'])) {
                            $uploaded = handleImageUpload($_FILES['seo_og_image_file']);
                            if ($uploaded === null) {
                                $error = 'Görsel yüklenemedi: dosya türü desteklenmiyor veya boyutu 5MB üzerinde.';
                            } else {
                                $site['settings']['seo_og_image'] = $uploaded;
                            }
                        }
                        $site['service_areas']['page_title'] = trim((string) ($_POST['areas_page_title'] ?? 'Hizmet Bölgelerimiz'));
                        $site['service_areas']['page_intro'] = trim((string) ($_POST['areas_page_intro'] ?? ''));
                        $site['service_areas']['areas'] = sanitizeServiceAreas($_POST);
                        break;
                    case 'texts':
                        $site['settings']['hero_kicker'] = trim((string) ($_POST['hero_kicker'] ?? ''));
                        $site['home'] = sanitizeHome($_POST);
                        $site['safety'] = sanitizeSafety($_POST);
                        $site['contact'] = sanitizeContact($_POST);
                        $site['founder'] = sanitizeFounder($_POST);
                        $site['hours'] = sanitizeHours($_POST);
                        break;
                    case 'pages':
                        $site['pages'] = sanitizePages($_POST, $site['pages']);
                        break;
                }
                if ($error !== null) {
                    // logo yükleme gibi ön adımlarda hata oluştuysa kaydetmeyi atla
                } elseif (!saveSiteContent($site)) {
                    $error = 'Kaydedilemedi. Sunucuda data/site.json dosyasına yazma izni olduğundan emin olun.';
                } else {
                    $saved = true;
                }
            } catch (Throwable $e) {
                $error = 'Beklenmeyen bir hata oluştu: ' . $e->getMessage();
            }
        }
    }
}

function sanitizeSettings(array $post): array
{
    $keys = ['site_name', 'legal_name', 'tagline', 'hero_video_id', 'hero_title', 'hero_subtitle',
        'hero_cta_primary', 'hero_cta_secondary', 'phone', 'phone_display', 'whatsapp', 'email',
        'notification_email', 'address', 'footer_text', 'instagram', 'linkedin', 'map_embed', 'preloader_text'];
    $out = [];
    foreach ($keys as $key) {
        $out[$key] = trim((string) ($post[$key] ?? ''));
    }
    $out['hero_video_id'] = youtubeId($out['hero_video_id']);
    $out['preloader_enabled'] = isset($post['preloader_enabled']);
    $out['preloader_duration'] = max(0, min(3000, (int) ($post['preloader_duration'] ?? 400)));
    return $out;
}

/** Yüklenen logo dosyasını doğrular, önceki özel logoyu temizler ve yenisini kaydeder. Başarısızsa null döner. */
function handleLogoUpload(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    if ((int) ($file['size'] ?? 0) <= 0 || (int) $file['size'] > LOGO_MAX_BYTES) {
        return null;
    }
    $tmpPath = (string) ($file['tmp_name'] ?? '');
    if (!is_uploaded_file($tmpPath)) {
        return null;
    }

    $ext = strtolower((string) pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if ($ext === 'jpeg') {
        $ext = 'jpg';
    }

    if ($ext === 'svg') {
        $content = (string) file_get_contents($tmpPath);
        if (!looksLikeSafeSvg($content)) {
            return null;
        }
    } else {
        $mime = detectImageMime($tmpPath);
        if ($mime === null || !isset(IMAGE_ALLOWED[$mime]) || IMAGE_ALLOWED[$mime] !== $ext) {
            return null;
        }
    }

    if (!in_array($ext, ['svg', 'png', 'jpg', 'webp'], true)) {
        return null;
    }

    removeCustomLogo();
    $target = PUBLIC_DIR . '/assets/img/logo-custom.' . $ext;
    if (!move_uploaded_file($tmpPath, $target)) {
        return null;
    }
    return '/assets/img/logo-custom.' . $ext;
}

/** Galeri görselleri (makine parkı, projeler, blog) için genel amaçlı yükleme. Benzersiz dosya adıyla kaydeder. */
function handleImageUpload(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    if ((int) ($file['size'] ?? 0) <= 0 || (int) $file['size'] > GALLERY_MAX_BYTES) {
        return null;
    }
    $tmpPath = (string) ($file['tmp_name'] ?? '');
    if (!is_uploaded_file($tmpPath)) {
        return null;
    }

    $ext = strtolower((string) pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if ($ext === 'jpeg') {
        $ext = 'jpg';
    }

    if ($ext === 'svg') {
        $content = (string) file_get_contents($tmpPath);
        if (!looksLikeSafeSvg($content)) {
            return null;
        }
    } else {
        $mime = detectImageMime($tmpPath);
        if ($mime === null || !isset(IMAGE_ALLOWED[$mime]) || IMAGE_ALLOWED[$mime] !== $ext) {
            return null;
        }
    }

    if (!in_array($ext, ['svg', 'png', 'jpg', 'webp'], true)) {
        return null;
    }

    $dir = PUBLIC_DIR . '/assets/img/uploads';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return null;
    }
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($tmpPath, $dir . '/' . $name)) {
        return null;
    }
    return '/assets/img/uploads/' . $name;
}

/** Başlıktan URL-dostu slug üretir (Türkçe karakterleri çevirir), verilen kümede benzersiz olacak şekilde sıra numarası ekler. */
function slugify(string $text, array $existing = []): string
{
    $map = ['ç' => 'c', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
        'Ç' => 'c', 'Ğ' => 'g', 'İ' => 'i', 'Ö' => 'o', 'Ş' => 's', 'Ü' => 'u'];
    $text = strtr($text, $map);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $slug = trim($text, '-');
    if ($slug === '') {
        $slug = 'oge';
    }
    $base = $slug;
    $i = 2;
    while (in_array($slug, $existing, true)) {
        $slug = $base . '-' . $i;
        $i++;
    }
    return $slug;
}

/**
 * Dosyanın gerçek MIME türünü tespit eder. Önce fileinfo eklentisini dener; eklenti
 * sunucuda etkin değilse (bazı paylaşımlı hostinglerde kapalı olabiliyor) yaygın
 * görsel formatlarının magic-byte imzalarına bakarak yedek bir tespit yapar.
 */
function detectImageMime(string $path): ?string
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo !== false) {
            $mime = finfo_file($finfo, $path);
            finfo_close($finfo);
            if ($mime !== false) {
                return $mime;
            }
        }
    }

    $bytes = file_get_contents($path, false, null, 0, 12);
    if ($bytes === false || strlen($bytes) < 4) {
        return null;
    }
    if (str_starts_with($bytes, "\x89PNG\r\n\x1a\n")) {
        return 'image/png';
    }
    if (str_starts_with($bytes, "\xFF\xD8\xFF")) {
        return 'image/jpeg';
    }
    if (strlen($bytes) >= 12 && substr($bytes, 0, 4) === 'RIFF' && substr($bytes, 8, 4) === 'WEBP') {
        return 'image/webp';
    }
    return null;
}

/** Basit SVG güvenlik kontrolü: script/olay işleyici/harici referans içeren dosyaları reddeder. */
function looksLikeSafeSvg(string $content): bool
{
    if (stripos($content, '<svg') === false) {
        return false;
    }
    $dangerous = ['<script', 'javascript:', 'data:text/html', 'onload=', 'onerror=', 'onclick=', '<foreignobject', '<iframe'];
    $lower = strtolower($content);
    foreach ($dangerous as $pattern) {
        if (str_contains($lower, $pattern)) {
            return false;
        }
    }
    return true;
}

/** Önceki özel logo dosyasını (hangi uzantıdaysa) siler. */
function removeCustomLogo(): void
{
    foreach (['svg', 'png', 'jpg', 'webp'] as $ext) {
        $path = PUBLIC_DIR . '/assets/img/logo-custom.' . $ext;
        if (is_file($path)) {
            unlink($path);
        }
    }
}

function sanitizeMenu(array $post): array
{
    $labels = $post['menu_label'] ?? [];
    $urls = $post['menu_url'] ?? [];
    $out = [];
    foreach ($labels as $i => $label) {
        $label = trim((string) $label);
        $url = trim((string) ($urls[$i] ?? ''));
        if ($label !== '' && $url !== '') {
            $out[] = ['label' => $label, 'url' => $url];
        }
    }
    return $out;
}

function sanitizeCounters(array $post): array
{
    $values = $post['counter_value'] ?? [];
    $suffixes = $post['counter_suffix'] ?? [];
    $labels = $post['counter_label'] ?? [];
    $out = [];
    foreach ($values as $i => $value) {
        $label = trim((string) ($labels[$i] ?? ''));
        if ($label === '') {
            continue;
        }
        $out[] = [
            'value' => (int) $value,
            'suffix' => trim((string) ($suffixes[$i] ?? '')),
            'label' => $label,
        ];
    }
    return $out;
}

function sanitizeServices(array $post): array
{
    $icons = $post['service_icon'] ?? [];
    $titles = $post['service_title'] ?? [];
    $descs = $post['service_desc'] ?? [];
    $contents = $post['service_content'] ?? [];
    $slugs = $post['service_slug'] ?? [];
    $existingSlugs = array_values(array_filter(array_map('strval', $slugs)));
    $out = [];
    foreach ($titles as $i => $title) {
        $title = trim((string) $title);
        if ($title === '') {
            continue;
        }
        $icon = (string) ($icons[$i] ?? 'crane');
        $slug = trim((string) ($slugs[$i] ?? ''));
        if ($slug === '') {
            $slug = slugify($title, $existingSlugs);
            $existingSlugs[] = $slug;
        }
        $out[] = [
            'slug' => $slug,
            'icon' => in_array($icon, ['crane', 'basket', 'truck', 'gear'], true) ? $icon : 'crane',
            'title' => $title,
            'desc' => trim((string) ($descs[$i] ?? '')),
            'content' => trim((string) ($contents[$i] ?? '')),
        ];
    }
    return $out;
}

/** name="field[]" şeklinde çoklu dosya inputundaki i. dosyayı tekil $_FILES formatına çevirir. */
function extractFileAt(array $files, string $field, int $i): ?array
{
    if (!isset($files[$field]['error'][$i]) || $files[$field]['error'][$i] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    return [
        'name' => $files[$field]['name'][$i],
        'type' => $files[$field]['type'][$i],
        'tmp_name' => $files[$field]['tmp_name'][$i],
        'error' => $files[$field]['error'][$i],
        'size' => $files[$field]['size'][$i],
    ];
}

/** Satırın mevcut (hidden alanla taşınan) görselini, varsa yeni yüklenen dosyayla değiştirir. */
function resolveRowImage(array $post, array $files, string $textField, string $fileField, int $i): string
{
    $current = trim((string) (($post[$textField] ?? [])[$i] ?? ''));
    $file = extractFileAt($files, $fileField, $i);
    if ($file !== null) {
        $uploaded = handleImageUpload($file);
        if ($uploaded !== null) {
            return $uploaded;
        }
    }
    return $current;
}

function sanitizeFleet(array $post, array $files): array
{
    $titles = $post['fleet_title'] ?? [];
    $capacities = $post['fleet_capacity'] ?? [];
    $descs = $post['fleet_desc'] ?? [];
    $contents = $post['fleet_content'] ?? [];
    $slugs = $post['fleet_slug'] ?? [];
    $existingSlugs = array_values(array_filter(array_map('strval', $slugs)));
    $out = [];
    foreach ($titles as $i => $title) {
        $title = trim((string) $title);
        if ($title === '') {
            continue;
        }
        $slug = trim((string) ($slugs[$i] ?? ''));
        if ($slug === '') {
            $slug = slugify($title, $existingSlugs);
            $existingSlugs[] = $slug;
        }
        $out[] = [
            'slug' => $slug,
            'title' => $title,
            'capacity' => trim((string) ($capacities[$i] ?? '')),
            'desc' => trim((string) ($descs[$i] ?? '')),
            'content' => trim((string) ($contents[$i] ?? '')),
            'image' => resolveRowImage($post, $files, 'fleet_image', 'fleet_image_file', $i),
        ];
    }
    return $out;
}

function sanitizeProjects(array $post, array $files): array
{
    $titles = $post['project_title'] ?? [];
    $clients = $post['project_client'] ?? [];
    $stats = $post['project_stat'] ?? [];
    $descs = $post['project_desc'] ?? [];
    $contents = $post['project_content'] ?? [];
    $slugs = $post['project_slug'] ?? [];
    $existingSlugs = array_values(array_filter(array_map('strval', $slugs)));
    $out = [];
    foreach ($titles as $i => $title) {
        $title = trim((string) $title);
        if ($title === '') {
            continue;
        }
        $slug = trim((string) ($slugs[$i] ?? ''));
        if ($slug === '') {
            $slug = slugify($title, $existingSlugs);
            $existingSlugs[] = $slug;
        }
        $out[] = [
            'slug' => $slug,
            'title' => $title,
            'client' => trim((string) ($clients[$i] ?? '')),
            'stat' => trim((string) ($stats[$i] ?? '')),
            'desc' => trim((string) ($descs[$i] ?? '')),
            'content' => trim((string) ($contents[$i] ?? '')),
            'image' => resolveRowImage($post, $files, 'project_image', 'project_image_file', $i),
        ];
    }
    return $out;
}

function sanitizeBlog(array $post, array $files): array
{
    $titles = $post['blog_title'] ?? [];
    $excerpts = $post['blog_excerpt'] ?? [];
    $contents = $post['blog_content'] ?? [];
    $dates = $post['blog_date'] ?? [];
    $slugs = $post['blog_slug'] ?? [];
    $published = $post['blog_published'] ?? [];
    $existingSlugs = array_values(array_filter(array_map('strval', $slugs)));
    $out = [];
    foreach ($titles as $i => $title) {
        $title = trim((string) $title);
        if ($title === '') {
            continue;
        }
        $slug = trim((string) ($slugs[$i] ?? ''));
        if ($slug === '') {
            $slug = slugify($title, $existingSlugs);
            $existingSlugs[] = $slug;
        }
        $date = trim((string) ($dates[$i] ?? ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }
        $out[] = [
            'slug' => $slug,
            'title' => $title,
            'excerpt' => trim((string) ($excerpts[$i] ?? '')),
            'content' => trim((string) ($contents[$i] ?? '')),
            'image' => resolveRowImage($post, $files, 'blog_image', 'blog_image_file', $i),
            'date' => $date,
            'published' => ($published[$i] ?? '0') === '1',
        ];
    }
    // en yeniden en eskiye sırala
    usort($out, static fn (array $a, array $b): int => strcmp($b['date'], $a['date']));
    return $out;
}

/** Referans galerisi: satır bazlı düzenleme + tekli görsel değişimi + toplu çoklu yükleme + sürükleyerek sıralama (DOM sırası = gönderim sırası). */
function sanitizeClients(array $post, array $files): array
{
    $names = $post['client_name'] ?? [];
    $out = [];
    foreach ($names as $i => $name) {
        $name = trim((string) $name);
        $logo = resolveRowImage($post, $files, 'client_logo', 'client_logo_file', (int) $i);
        if ($name === '' && $logo === '') {
            continue;
        }
        $out[] = ['name' => $name, 'logo' => $logo];
    }

    // Toplu yükleme: tek seferde seçilen tüm dosyalar, listenin sonuna yeni referans olarak eklenir.
    $bulkNames = $files['client_bulk_file']['name'] ?? [];
    foreach ($bulkNames as $i => $originalName) {
        if (($originalName ?? '') === '') {
            continue;
        }
        $file = extractFileAt($files, 'client_bulk_file', (int) $i);
        if ($file === null) {
            continue;
        }
        $uploaded = handleImageUpload($file);
        if ($uploaded === null) {
            continue;
        }
        $out[] = ['name' => filenameToLabel((string) $originalName), 'logo' => $uploaded];
    }

    return $out;
}

/** "acme-corp-logo.png" gibi bir dosya adını "Acme Corp Logo" gibi okunabilir bir isme çevirir (mbstring eklentisine bağımlı değildir). */
function filenameToLabel(string $filename): string
{
    $name = (string) pathinfo($filename, PATHINFO_FILENAME);
    $name = str_replace(['-', '_'], ' ', $name);
    $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');
    if ($name === '') {
        return 'Referans';
    }
    $words = array_map(static function (string $word): string {
        if ($word === '') {
            return $word;
        }
        $first = firstChar($word);
        return strtoupper($first) . substr($word, strlen($first));
    }, explode(' ', $name));
    return implode(' ', $words);
}

function sanitizeHome(array $post): array
{
    $keys = ['services_kicker', 'services_title', 'fleet_kicker', 'fleet_title',
        'projects_kicker', 'projects_title', 'cta_title', 'cta_subtitle'];
    $out = [];
    foreach ($keys as $key) {
        $out[$key] = trim((string) ($post['home_' . $key] ?? ''));
    }
    return $out;
}

function sanitizeSafety(array $post): array
{
    $titles = $post['safety_title'] ?? [];
    $descs = $post['safety_desc'] ?? [];
    $out = [];
    foreach ($titles as $i => $title) {
        $title = trim((string) $title);
        if ($title === '') {
            continue;
        }
        $out[] = ['title' => $title, 'desc' => trim((string) ($descs[$i] ?? ''))];
    }
    return $out;
}

function sanitizeContact(array $post): array
{
    $keys = ['hero_title', 'hero_subtitle', 'info_title', 'form_title'];
    $out = [];
    foreach ($keys as $key) {
        $out[$key] = trim((string) ($post['contact_' . $key] ?? ''));
    }
    return $out;
}

function sanitizeFounder(array $post): array
{
    return [
        'name' => trim((string) ($post['founder_name'] ?? '')),
        'title' => trim((string) ($post['founder_title'] ?? '')),
    ];
}

function sanitizeHours(array $post): array
{
    $days = $post['hours_day'] ?? [];
    $values = $post['hours_value'] ?? [];
    $out = [];
    foreach ($days as $i => $day) {
        $day = trim((string) $day);
        if ($day === '') {
            continue;
        }
        $out[] = ['day' => $day, 'value' => trim((string) ($values[$i] ?? ''))];
    }
    return $out;
}

function sanitizeServiceAreas(array $post): array
{
    $names = $post['area_name'] ?? [];
    $descs = $post['area_desc'] ?? [];
    $out = [];
    foreach ($names as $i => $name) {
        $name = trim((string) $name);
        if ($name === '') {
            continue;
        }
        $out[] = ['name' => $name, 'desc' => trim((string) ($descs[$i] ?? ''))];
    }
    return $out;
}

function sanitizePages(array $post, array $existing): array
{
    foreach ($existing as $slug => $page) {
        $existing[$slug]['title'] = trim((string) ($post['page_title_' . $slug] ?? $page['title']));
        $existing[$slug]['body'] = trim((string) ($post['page_body_' . $slug] ?? $page['body']));
    }
    return $existing;
}

require APP_DIR . '/templates/admin/render.php';
renderAdminPanel($site, $saved, $error);
