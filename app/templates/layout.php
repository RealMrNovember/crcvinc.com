<?php /** @var array $settings @var string $content */ ?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) && $pageTitle ? e($pageTitle) . ' — ' : '' ?><?= e($settings['site_name']) ?> | <?= e($settings['tagline']) ?></title>
<meta name="description" content="<?= e($settings['footer_text']) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(assetUrl('/assets/css/main.css')) ?>">
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => $settings['site_name'],
    'legalName' => $settings['legal_name'] ?? $settings['site_name'],
    'telephone' => $settings['phone'],
    'email' => $settings['email'],
    'address' => $settings['address'],
    'url' => 'https://www.crcvinc.com',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>

</script>
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<?= render('partials/header', ['settings' => $settings, 'site' => $site]) ?>
<main>
<?= $content ?>
</main>
<?= render('partials/footer', ['settings' => $settings, 'site' => $site]) ?>
<a class="whatsapp-fab" href="https://wa.me/<?= e($settings['whatsapp']) ?>" target="_blank" rel="noopener" aria-label="WhatsApp ile yazın">
  <svg viewBox="0 0 32 32" width="28" height="28" fill="currentColor" aria-hidden="true"><path d="M16 3C9.4 3 4 8.4 4 15c0 2.1.6 4.2 1.6 6L4 29l8.2-1.5c1.2.5 2.5.7 3.8.7 6.6 0 12-5.4 12-12S22.6 3 16 3zm0 21.8c-1.2 0-2.4-.2-3.5-.7l-.5-.2-4.9.9 1-4.7-.3-.5c-1-1.6-1.5-3.4-1.5-5.3 0-5.4 4.4-9.8 9.8-9.8s9.8 4.4 9.8 9.8-4.5 9.5-9.9 9.5zm5.4-7.1c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-1 1.2-.2.2-.4.2-.7.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.5-.6c.2-.2.2-.4.3-.6.1-.2 0-.4 0-.6-.1-.1-.7-1.7-1-2.3-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.1 1.1-1.1 2.7s1.2 3.1 1.3 3.3c.2.2 2.3 3.5 5.5 4.9.8.3 1.4.5 1.9.7.8.2 1.5.2 2 .1.6-.1 1.8-.8 2.1-1.5.3-.7.3-1.3.2-1.5-.1-.1-.3-.2-.6-.3z"/></svg>
</a>
<script src="<?= e(assetUrl('/assets/js/main.js')) ?>" defer></script>
</body>
</html>
