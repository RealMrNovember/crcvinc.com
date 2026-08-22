<?php
declare(strict_types=1);

define('APP_DIR', __DIR__);
define('DATA_DIR', dirname(__DIR__) . '/data');
define('TEMPLATE_DIR', APP_DIR . '/templates');
define('PUBLIC_DIR', dirname(__DIR__) . '/public_html');

// Sunucudaki PHP sürümü 8.0'dan eski olabilir (tespit edildi) — bu üç fonksiyon PHP 8.0
// ile geldi, projede birden çok yerde kullanılıyor. Yoklarsa burada tanımlanır.
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

require APP_DIR . '/content.php';

/** CSS/JS linklerine dosyanın son değişim zamanını ?v= olarak ekler — Cloudflare/tarayıcı önbelleği her deploy'da otomatik kırılır. */
function assetUrl(string $path): string
{
    $file = PUBLIC_DIR . '/' . ltrim($path, '/');
    $mtime = @filemtime($file);
    return $path . ($mtime !== false ? '?v=' . $mtime : '');
}

/** UTF-8 metnin ilk karakterini döndürür — mbstring eklentisine bağımlı değildir. */
function firstChar(string $text): string
{
    preg_match('/^./u', $text, $matches);
    return $matches[0] ?? '';
}

/** Marka logosunun URL'ini döndürür — admin panelden yüklenmiş özel logo varsa onu, yoksa varsayılan CRC amblemini kullanır. */
function brandLogoUrl(array $settings): string
{
    $custom = trim((string) ($settings['logo_path'] ?? ''));
    return $custom !== '' ? $custom : '/assets/img/logo-mark-light.svg';
}

/** "2026-08-01" biçimindeki tarihi "1 Ağustos 2026" olarak Türkçe biçimlendirir. */
function formatTurkishDate(string $date): string
{
    $months = [1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
        'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
    $parts = explode('-', $date);
    if (count($parts) !== 3) {
        return $date;
    }
    [$y, $m, $d] = $parts;
    $monthIndex = (int) $m;
    if ($monthIndex < 1 || $monthIndex > 12) {
        return $date;
    }
    return ((int) $d) . ' ' . $months[$monthIndex] . ' ' . $y;
}

/** Ana sayfa ve tüm sayfalarda kullanılan LocalBusiness/HomeAndConstructionBusiness yapılandırılmış verisini üretir. */
function buildLocalBusinessSchema(array $settings, array $site, string $origin): array
{
    $dayMap = [
        'Pazartesi' => 'Monday', 'Salı' => 'Tuesday', 'Çarşamba' => 'Wednesday',
        'Perşembe' => 'Thursday', 'Cuma' => 'Friday', 'Cumartesi' => 'Saturday', 'Pazar' => 'Sunday',
    ];
    $hoursSpec = [];
    foreach ($site['hours'] ?? [] as $item) {
        $day = $dayMap[$item['day'] ?? ''] ?? null;
        $value = trim((string) ($item['value'] ?? ''));
        if ($day === null || $value === '' || stripos($value, 'kapalı') !== false) {
            continue;
        }
        $parts = preg_split('/\s*[–-]\s*/u', $value);
        if (count($parts) !== 2) {
            continue;
        }
        $hoursSpec[] = [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => 'https://schema.org/' . $day,
            'opens' => trim($parts[0]),
            'closes' => trim($parts[1]),
        ];
    }

    $sameAs = array_values(array_filter([$settings['instagram'] ?? '', $settings['linkedin'] ?? '']));

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'HomeAndConstructionBusiness',
        'name' => $settings['site_name'],
        'legalName' => $settings['legal_name'] ?? $settings['site_name'],
        'description' => $settings['seo_default_description'] ?? $settings['footer_text'],
        'telephone' => $settings['phone'],
        'email' => $settings['email'],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $settings['address'],
            'addressCountry' => 'TR',
        ],
        'areaServed' => 'Ankara',
        'url' => $origin,
    ];

    $logo = brandLogoUrl($settings);
    if ($logo !== '') {
        $schema['image'] = str_starts_with($logo, 'http') ? $logo : $origin . assetUrl($logo);
    }
    if ($hoursSpec !== []) {
        $schema['openingHoursSpecification'] = $hoursSpec;
    }
    if ($sameAs !== []) {
        $schema['sameAs'] = $sameAs;
    }

    return $schema;
}

/** Bir dizi içinden 'slug' alanı eşleşen ilk öğeyi döndürür, yoksa null. */
function findBySlug(array $items, string $slug): ?array
{
    foreach ($items as $item) {
        if (($item['slug'] ?? '') === $slug) {
            return $item;
        }
    }
    return null;
}

/** HTML çıktısı için güvenli kaçış. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Düz metni paragraf etiketlerine çevirir (admin "body" alanları için). */
function paragraphs(?string $text): string
{
    if ($text === null || trim($text) === '') {
        return '';
    }
    $blocks = preg_split('/\R{2,}/u', trim($text)) ?: [];
    $html = '';
    foreach ($blocks as $block) {
        $html .= '<p>' . nl2br(e(trim($block))) . '</p>';
    }
    return $html;
}

/** Şablonu verilen değişkenlerle çalıştırıp çıktıyı döndürür. */
function render(string $template, array $vars = []): string
{
    $file = TEMPLATE_DIR . '/' . $template . '.php';
    if (!is_file($file)) {
        throw new RuntimeException("Şablon bulunamadı: {$template}");
    }
    extract($vars, EXTR_SKIP);
    ob_start();
    require $file;
    return (string) ob_get_clean();
}

/** Sayfayı ana yerleşim (layout) içinde basar. */
function renderPage(string $template, array $vars = []): void
{
    $site = siteContent();
    $vars['site'] = $site;
    $vars['settings'] = $site['settings'];
    $content = render($template, $vars);
    echo render('layout', $vars + ['content' => $content]);
}
