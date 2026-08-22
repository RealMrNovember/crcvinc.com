<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

$site = siteContent();

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

    case '/iletisim':
        require APP_DIR . '/contact.php';
        handleContactPage();
        break;

    default:
        http_response_code(404);
        renderPage('page', [
            'pageTitle' => 'Sayfa Bulunamadı',
            'page' => ['title' => 'Sayfa Bulunamadı', 'body' => 'Aradığınız sayfa mevcut değil.'],
        ]);
}
