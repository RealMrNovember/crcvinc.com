<?php
// Sadece lokal geliştirme: php -S localhost:8080 router.php
// .htaccess davranışını taklit eder — gerçek dosyalar servis edilir, kalanlar index.php'ye gider.
declare(strict_types=1);

$root = __DIR__ . '/public_html';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = realpath($root . $path);

if ($file !== false && str_starts_with($file, realpath($root)) && is_file($file)) {
    if (str_ends_with($file, '.php')) {
        require $file;
        return true;
    }
    return false; // statik dosyayı sunucu servis etsin
}

if (str_starts_with($path, '/admin')) {
    require $root . '/admin/index.php';
    return true;
}

require $root . '/index.php';
