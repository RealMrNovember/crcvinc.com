<?php
declare(strict_types=1);

/** sitemap.xml'i dinamik olarak üretir — her zaman birincil (kanonik) domain üzerinden. */
function renderSitemap(array $site): void
{
    $origin = 'https://www.crcvinc.com';
    $urls = [
        ['loc' => $origin . '/', 'priority' => '1.0'],
        ['loc' => $origin . '/hizmetler', 'priority' => '0.8'],
        ['loc' => $origin . '/makine-parki', 'priority' => '0.8'],
        ['loc' => $origin . '/projeler', 'priority' => '0.8'],
        ['loc' => $origin . '/kurumsal', 'priority' => '0.6'],
        ['loc' => $origin . '/blog', 'priority' => '0.6'],
        ['loc' => $origin . '/iletisim', 'priority' => '0.7'],
    ];

    foreach ($site['services'] ?? [] as $item) {
        if (!empty($item['slug'])) {
            $urls[] = ['loc' => $origin . '/hizmetler/' . $item['slug'], 'priority' => '0.6'];
        }
    }
    foreach ($site['fleet'] ?? [] as $item) {
        if (!empty($item['slug'])) {
            $urls[] = ['loc' => $origin . '/makine-parki/' . $item['slug'], 'priority' => '0.6'];
        }
    }
    foreach ($site['projects'] ?? [] as $item) {
        if (!empty($item['slug'])) {
            $urls[] = ['loc' => $origin . '/projeler/' . $item['slug'], 'priority' => '0.6'];
        }
    }
    foreach ($site['blog']['posts'] ?? [] as $post) {
        if (!empty($post['published']) && !empty($post['slug'])) {
            $urls[] = ['loc' => $origin . '/blog/' . $post['slug'], 'priority' => '0.5', 'lastmod' => $post['date'] ?? null];
        }
    }

    header('Content-Type: application/xml; charset=UTF-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $url) {
        echo "  <url>\n";
        echo '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
        if (!empty($url['lastmod'])) {
            echo '    <lastmod>' . e($url['lastmod']) . "</lastmod>\n";
        }
        echo '    <priority>' . $url['priority'] . "</priority>\n";
        echo "  </url>\n";
    }
    echo '</urlset>' . "\n";
}
