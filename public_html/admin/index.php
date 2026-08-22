<?php
declare(strict_types=1);

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
        try {
            switch ($tab) {
                case 'settings':
                    $site['settings'] = array_merge($site['settings'], sanitizeSettings($_POST));
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
                    $site['fleet'] = sanitizeFleet($_POST);
                    break;
                case 'projects':
                    $site['projects'] = sanitizeProjects($_POST);
                    break;
                case 'clients':
                    $site['clients'] = sanitizeClients($_POST);
                    break;
                case 'pages':
                    $site['pages'] = sanitizePages($_POST, $site['pages']);
                    break;
            }
            if (!saveSiteContent($site)) {
                $error = 'Kaydedilemedi. Sunucuda data/site.json dosyasına yazma izni olduğundan emin olun.';
            } else {
                $saved = true;
            }
        } catch (Throwable $e) {
            $error = 'Beklenmeyen bir hata oluştu: ' . $e->getMessage();
        }
    }
}

function sanitizeSettings(array $post): array
{
    $keys = ['site_name', 'legal_name', 'tagline', 'hero_video_id', 'hero_title', 'hero_subtitle',
        'hero_cta_primary', 'hero_cta_secondary', 'phone', 'phone_display', 'whatsapp', 'email',
        'address', 'footer_text', 'instagram', 'linkedin', 'map_embed'];
    $out = [];
    foreach ($keys as $key) {
        $out[$key] = trim((string) ($post[$key] ?? ''));
    }
    $out['hero_video_id'] = youtubeId($out['hero_video_id']);
    return $out;
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
    $out = [];
    foreach ($titles as $i => $title) {
        $title = trim((string) $title);
        if ($title === '') {
            continue;
        }
        $icon = (string) ($icons[$i] ?? 'crane');
        $out[] = [
            'icon' => in_array($icon, ['crane', 'basket', 'truck', 'gear'], true) ? $icon : 'crane',
            'title' => $title,
            'desc' => trim((string) ($descs[$i] ?? '')),
        ];
    }
    return $out;
}

function sanitizeFleet(array $post): array
{
    $titles = $post['fleet_title'] ?? [];
    $capacities = $post['fleet_capacity'] ?? [];
    $descs = $post['fleet_desc'] ?? [];
    $images = $post['fleet_image'] ?? [];
    $out = [];
    foreach ($titles as $i => $title) {
        $title = trim((string) $title);
        if ($title === '') {
            continue;
        }
        $out[] = [
            'title' => $title,
            'capacity' => trim((string) ($capacities[$i] ?? '')),
            'desc' => trim((string) ($descs[$i] ?? '')),
            'image' => trim((string) ($images[$i] ?? '')),
        ];
    }
    return $out;
}

function sanitizeProjects(array $post): array
{
    $titles = $post['project_title'] ?? [];
    $clients = $post['project_client'] ?? [];
    $stats = $post['project_stat'] ?? [];
    $descs = $post['project_desc'] ?? [];
    $images = $post['project_image'] ?? [];
    $out = [];
    foreach ($titles as $i => $title) {
        $title = trim((string) $title);
        if ($title === '') {
            continue;
        }
        $out[] = [
            'title' => $title,
            'client' => trim((string) ($clients[$i] ?? '')),
            'stat' => trim((string) ($stats[$i] ?? '')),
            'desc' => trim((string) ($descs[$i] ?? '')),
            'image' => trim((string) ($images[$i] ?? '')),
        ];
    }
    return $out;
}

function sanitizeClients(array $post): array
{
    $raw = (string) ($post['clients_raw'] ?? '');
    $lines = preg_split('/\R/', $raw) ?: [];
    $out = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {
            $out[] = $line;
        }
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
