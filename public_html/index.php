<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

$site = siteContent();

function render404(): void
{
    http_response_code(404);
    renderPage('page', [
        'pageTitle' => 'Sayfa Bulunamadı',
        'metaDescription' => 'Aradığınız sayfa mevcut değil.',
        'page' => ['title' => 'Sayfa Bulunamadı', 'body' => 'Aradığınız sayfa mevcut değil.'],
    ]);
}

/** Bir metni belirtilen uzunlukta, kelime ortasından kesmeden meta açıklamaya çevirir (mbstring'e bağımlı değildir). */
function excerptFor(string $text, int $length = 155): string
{
    $text = trim((string) preg_replace('/\s+/u', ' ', $text));
    $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    if (count($chars) <= $length) {
        return $text;
    }
    $cut = $chars;
    for ($i = $length; $i > 0; $i--) {
        if ($chars[$i - 1] === ' ') {
            $cut = array_slice($chars, 0, $i - 1);
            break;
        }
        if ($i === 1) {
            $cut = array_slice($chars, 0, $length);
        }
    }
    return implode('', $cut) . '…';
}

if ($path === '/sitemap.xml') {
    require APP_DIR . '/sitemap.php';
    renderSitemap($site);
    return;
}

if (preg_match('#^/hizmetler/([a-z0-9-]+)$#', $path, $m)) {
    $service = findBySlug($site['services'], $m[1]);
    if ($service === null) {
        render404();
    } else {
        renderPage('service-detail', [
            'pageTitle' => $service['title'],
            'metaDescription' => excerptFor($service['desc']),
            'service' => $service,
        ]);
    }
    return;
}

if (preg_match('#^/makine-parki/([a-z0-9-]+)$#', $path, $m)) {
    $fleetItem = findBySlug($site['fleet'], $m[1]);
    if ($fleetItem === null) {
        render404();
    } else {
        renderPage('fleet-detail', [
            'pageTitle' => $fleetItem['title'],
            'metaDescription' => excerptFor($fleetItem['desc']),
            'ogImage' => $fleetItem['image'] ?? '',
            'item' => $fleetItem,
        ]);
    }
    return;
}

if (preg_match('#^/projeler/([a-z0-9-]+)$#', $path, $m)) {
    $project = findBySlug($site['projects'], $m[1]);
    if ($project === null) {
        render404();
    } else {
        renderPage('project-detail', [
            'pageTitle' => $project['title'],
            'metaDescription' => excerptFor($project['desc']),
            'ogImage' => $project['image'] ?? '',
            'project' => $project,
        ]);
    }
    return;
}

if (preg_match('#^/blog/([a-z0-9-]+)$#', $path, $m)) {
    $post = findBySlug($site['blog']['posts'] ?? [], $m[1]);
    if ($post === null || empty($post['published'])) {
        render404();
    } else {
        renderPage('blog-detail', [
            'pageTitle' => $post['title'],
            'metaDescription' => excerptFor($post['excerpt'] !== '' ? $post['excerpt'] : $post['content']),
            'ogImage' => $post['image'] ?? '',
            'post' => $post,
        ]);
    }
    return;
}

switch ($path) {
    case '/':
        renderPage('home', [
            'pageTitle' => null,
            'metaDescription' => $site['settings']['hero_subtitle'] ?? null,
            'bodyClass' => 'is-home',
        ]);
        break;

    case '/hizmetler':
        renderPage('services', [
            'pageTitle' => $site['pages']['hizmetler']['title'] ?? 'Hizmetler',
            'metaDescription' => excerptFor($site['pages']['hizmetler']['body'] ?? ''),
            'page' => $site['pages']['hizmetler'] ?? [],
        ]);
        break;

    case '/makine-parki':
        renderPage('fleet', [
            'pageTitle' => $site['pages']['makine-parki']['title'] ?? 'Makine Parkı',
            'metaDescription' => excerptFor($site['pages']['makine-parki']['body'] ?? ''),
            'page' => $site['pages']['makine-parki'] ?? [],
        ]);
        break;

    case '/projeler':
        renderPage('projects', [
            'pageTitle' => $site['pages']['projeler']['title'] ?? 'Projeler',
            'metaDescription' => excerptFor($site['pages']['projeler']['body'] ?? ''),
            'page' => $site['pages']['projeler'] ?? [],
        ]);
        break;

    case '/kurumsal':
        renderPage('about', [
            'pageTitle' => $site['pages']['kurumsal']['title'] ?? 'Kurumsal',
            'metaDescription' => excerptFor($site['pages']['kurumsal']['body'] ?? ''),
            'page' => $site['pages']['kurumsal'] ?? [],
        ]);
        break;

    case '/blog':
        renderPage('blog', [
            'pageTitle' => $site['blog']['page_title'] ?? 'Blog',
            'metaDescription' => excerptFor($site['blog']['page_intro'] ?? ''),
            'posts' => array_values(array_filter($site['blog']['posts'] ?? [], static fn (array $p): bool => !empty($p['published']))),
        ]);
        break;

    case '/hizmet-bolgelerimiz':
        renderPage('service-areas', [
            'pageTitle' => $site['service_areas']['page_title'] ?? 'Hizmet Bölgelerimiz',
            'metaDescription' => excerptFor($site['service_areas']['page_intro'] ?? ''),
        ]);
        break;

    case '/iletisim':
        require APP_DIR . '/contact.php';
        handleContactPage();
        break;

    default:
        render404();
}
