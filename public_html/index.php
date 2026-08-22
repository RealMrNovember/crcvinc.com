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
        'page' => ['title' => 'Sayfa Bulunamadı', 'body' => 'Aradığınız sayfa mevcut değil.'],
    ]);
}

if (preg_match('#^/projeler/([a-z0-9-]+)$#', $path, $m)) {
    $project = findBySlug($site['projects'], $m[1]);
    if ($project === null) {
        render404();
    } else {
        renderPage('project-detail', ['pageTitle' => $project['title'], 'project' => $project]);
    }
    return;
}

if (preg_match('#^/blog/([a-z0-9-]+)$#', $path, $m)) {
    $post = findBySlug($site['blog']['posts'] ?? [], $m[1]);
    if ($post === null || empty($post['published'])) {
        render404();
    } else {
        renderPage('blog-detail', ['pageTitle' => $post['title'], 'post' => $post]);
    }
    return;
}

switch ($path) {
    case '/':
        renderPage('home', ['pageTitle' => null, 'bodyClass' => 'is-home']);
        break;

    case '/hizmetler':
        renderPage('services', [
            'pageTitle' => $site['pages']['hizmetler']['title'] ?? 'Hizmetler',
            'page' => $site['pages']['hizmetler'] ?? [],
        ]);
        break;

    case '/makine-parki':
        renderPage('fleet', [
            'pageTitle' => $site['pages']['makine-parki']['title'] ?? 'Makine Parkı',
            'page' => $site['pages']['makine-parki'] ?? [],
        ]);
        break;

    case '/projeler':
        renderPage('projects', [
            'pageTitle' => $site['pages']['projeler']['title'] ?? 'Projeler',
            'page' => $site['pages']['projeler'] ?? [],
        ]);
        break;

    case '/kurumsal':
        renderPage('about', [
            'pageTitle' => $site['pages']['kurumsal']['title'] ?? 'Kurumsal',
            'page' => $site['pages']['kurumsal'] ?? [],
        ]);
        break;

    case '/blog':
        renderPage('blog', [
            'pageTitle' => $site['blog']['page_title'] ?? 'Blog',
            'posts' => array_values(array_filter($site['blog']['posts'] ?? [], static fn (array $p): bool => !empty($p['published']))),
        ]);
        break;

    case '/iletisim':
        require APP_DIR . '/contact.php';
        handleContactPage();
        break;

    default:
        render404();
}
