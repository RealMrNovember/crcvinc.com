<?php
/** @var array $settings @var array $site @var string $content */

// SEO: tüm sayfalar (crcvinc.com ve crcvinc.com.tr dahil) tek bir birincil alan adını
// kanonik gösterir — aynı içeriğin iki domainde yayında olmasının Google'da "duplicate
// content" sayılıp sıralama gücünün bölünmesini önler.
const SEO_PRIMARY_ORIGIN = 'https://www.crcvinc.com';
$seoPath = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$seoCanonical = SEO_PRIMARY_ORIGIN . ($seoPath === '/' ? '' : rtrim($seoPath, '/'));
$seoTitle = (isset($pageTitle) && $pageTitle ? $pageTitle . ' — ' : '') . $settings['site_name'] . ' | ' . $settings['tagline'];
$seoDescription = $metaDescription ?? ($settings['seo_default_description'] ?? $settings['footer_text']);
$seoImage = $ogImage ?? ($settings['seo_og_image'] ?? '');
$seoImageUrl = $seoImage !== '' ? (str_starts_with($seoImage, 'http') ? $seoImage : SEO_PRIMARY_ORIGIN . assetUrl($seoImage)) : '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($seoTitle) ?></title>
<meta name="description" content="<?= e($seoDescription) ?>">
<link rel="canonical" href="<?= e($seoCanonical) ?>">
<meta name="robots" content="index, follow">
<?php if (!empty($settings['seo_gsc_verification'])): ?>
<meta name="google-site-verification" content="<?= e($settings['seo_gsc_verification']) ?>">
<?php endif; ?>

<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e($settings['site_name']) ?>">
<meta property="og:locale" content="tr_TR">
<meta property="og:title" content="<?= e($seoTitle) ?>">
<meta property="og:description" content="<?= e($seoDescription) ?>">
<meta property="og:url" content="<?= e($seoCanonical) ?>">
<?php if ($seoImageUrl !== ''): ?>
<meta property="og:image" content="<?= e($seoImageUrl) ?>">
<?php endif; ?>
<meta name="twitter:card" content="<?= $seoImageUrl !== '' ? 'summary_large_image' : 'summary' ?>">
<meta name="twitter:title" content="<?= e($seoTitle) ?>">
<meta name="twitter:description" content="<?= e($seoDescription) ?>">
<?php if ($seoImageUrl !== ''): ?>
<meta name="twitter:image" content="<?= e($seoImageUrl) ?>">
<?php endif; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(assetUrl('/assets/css/main.css')) ?>">
<link rel="icon" type="image/svg+xml" href="<?= e(assetUrl('/assets/img/favicon.svg')) ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= e(assetUrl('/assets/img/favicon-32.png')) ?>">
<link rel="apple-touch-icon" href="<?= e(assetUrl('/assets/img/apple-touch-icon.png')) ?>">
<?php if (!empty($settings['seo_ga_id'])): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($settings['seo_ga_id']) ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', <?= json_encode($settings['seo_ga_id']) ?>);
</script>
<?php endif; ?>
<script type="application/ld+json">
<?= json_encode(buildLocalBusinessSchema($settings, $site, SEO_PRIMARY_ORIGIN), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>

</script>
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<?php if ($settings['preloader_enabled'] ?? true): ?>
<?= render('partials/preloader', ['settings' => $settings]) ?>
<?php endif; ?>
<?= render('partials/header', ['settings' => $settings, 'site' => $site]) ?>
<?= render('partials/mobile-menu', ['site' => $site]) ?>
<main>
<?= $content ?>
</main>
<?= render('partials/footer', ['settings' => $settings, 'site' => $site]) ?>
<a class="whatsapp-fab" href="https://wa.me/<?= e($settings['whatsapp']) ?>" target="_blank" rel="noopener" aria-label="WhatsApp ile yazın">
  <svg viewBox="0 0 32 32" width="28" height="28" fill="currentColor" aria-hidden="true"><path d="M16 3C9.4 3 4 8.4 4 15c0 2.1.6 4.2 1.6 6L4 29l8.2-1.5c1.2.5 2.5.7 3.8.7 6.6 0 12-5.4 12-12S22.6 3 16 3zm0 21.8c-1.2 0-2.4-.2-3.5-.7l-.5-.2-4.9.9 1-4.7-.3-.5c-1-1.6-1.5-3.4-1.5-5.3 0-5.4 4.4-9.8 9.8-9.8s9.8 4.4 9.8 9.8-4.5 9.5-9.9 9.5zm5.4-7.1c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-1 1.2-.2.2-.4.2-.7.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.5-.6c.2-.2.2-.4.3-.6.1-.2 0-.4 0-.6-.1-.1-.7-1.7-1-2.3-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.1 1.1-1.1 2.7s1.2 3.1 1.3 3.3c.2.2 2.3 3.5 5.5 4.9.8.3 1.4.5 1.9.7.8.2 1.5.2 2 .1.6-.1 1.8-.8 2.1-1.5.3-.7.3-1.3.2-1.5-.1-.1-.3-.2-.6-.3z"/></svg>
</a>
<?= render('partials/bottom-nav', ['settings' => $settings]) ?>
<script src="<?= e(assetUrl('/assets/js/main.js')) ?>" defer></script>
</body>
</html>
