<?php
// Sadece lokal geliştirme: php -S localhost:8080 router.php
// .htaccess davranışını taklit eder — gerçek dosyalar servis edilir, kalanlar index.php'ye gider.
declare(strict_types=1);

$root = __DIR__ . '/public_html';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = realpath($root . $path);

$rootReal = realpath($root);
if ($file !== false && strncmp($file, $rootReal, strlen($rootReal)) === 0 && is_file($file)) {
    if (substr($file, -4) === '.php') {
        require $file;
        return true;
    }
    return false; // statik dosyayı sunucu servis etsin
}

if (strncmp($path, '/admin', 6) === 0) {
    require $root . '/admin/index.php';
    return true;
}

require $root . '/index.php';
