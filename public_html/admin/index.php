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
