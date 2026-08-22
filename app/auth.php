<?php
declare(strict_types=1);

/** Basit tek kullanıcılı admin oturumu: data/admin.json içinde password_hash saklar. */

function adminCredentialsFile(): string
{
    return DATA_DIR . '/admin.json';
}

function adminCredentialsExist(): bool
{
    return is_file(adminCredentialsFile());
}

function saveAdminCredentials(string $username, string $password): bool
{
    return saveAdminCredentialsRaw($username, password_hash($password, PASSWORD_DEFAULT));
}

function saveAdminCredentialsRaw(string $username, string $passwordHash): bool
{
    $payload = ['username' => $username, 'password_hash' => $passwordHash];
    $json = json_encode($payload, JSON_PRETTY_PRINT);
    return $json !== false && file_put_contents(adminCredentialsFile(), $json, LOCK_EX) !== false;
}

function currentAdminUsername(): string
{
    $data = json_decode((string) @file_get_contents(adminCredentialsFile()), true);
    return is_array($data) ? (string) ($data['username'] ?? '') : '';
}

/**
 * Kullanıcı adını ve (isteğe bağlı) şifreyi değiştirir. Her zaman mevcut şifreyi doğrular.
 * @return array{0: bool, 1: string} [başarılı mı, hata mesajı]
 */
function changeAdminCredentials(string $currentPassword, string $newUsername, ?string $newPassword): array
{
    $data = json_decode((string) @file_get_contents(adminCredentialsFile()), true);
    if (!is_array($data)) {
        return [false, 'Mevcut hesap bilgileri okunamadı.'];
    }
    if (!password_verify($currentPassword, (string) ($data['password_hash'] ?? ''))) {
        return [false, 'Mevcut şifre yanlış.'];
    }

    $newUsername = trim($newUsername);
    if ($newUsername === '') {
        return [false, 'Kullanıcı adı boş olamaz.'];
    }

    $passwordHash = (string) $data['password_hash'];
    if ($newPassword !== null && $newPassword !== '') {
        if (strlen($newPassword) < 8) {
            return [false, 'Yeni şifre en az 8 karakter olmalı.'];
        }
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    if (!saveAdminCredentialsRaw($newUsername, $passwordHash)) {
        return [false, 'Kaydedilemedi. data/ klasörüne yazma izni olduğundan emin olun.'];
    }
    return [true, ''];
}

/**
 * IP başına başarısız giriş denemesi sayacı — session çerezini silen saldırgana karşı ek koruma.
 * Cloudflare arkasında CF-Connecting-IP kullanılır (istemci tarafından taklit edilemez).
 */
const LOGIN_THROTTLE_WINDOW = 900; // 15 dakika
const LOGIN_THROTTLE_LIMIT = 10;

function loginThrottleFile(): string
{
    return DATA_DIR . '/login_throttle.json';
}

function clientIp(): string
{
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return (string) $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    return (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
}

function readLoginThrottle(): array
{
    $raw = @file_get_contents(loginThrottleFile());
    $decoded = $raw !== false ? json_decode($raw, true) : null;
    return is_array($decoded) ? $decoded : [];
}

/** Yazarken pencere dışına çıkmış eski girdileri de temizler ki dosya büyümesin. */
function writeLoginThrottle(array $data): void
{
    $now = time();
    foreach ($data as $ip => $entry) {
        if (($now - (int) ($entry['first'] ?? 0)) > LOGIN_THROTTLE_WINDOW) {
            unset($data[$ip]);
        }
    }
    $json = json_encode($data, JSON_PRETTY_PRINT);
    if ($json !== false) {
        file_put_contents(loginThrottleFile(), $json, LOCK_EX);
    }
}

function isIpThrottled(): bool
{
    $entry = readLoginThrottle()[clientIp()] ?? null;
    if ($entry === null || (time() - (int) $entry['first']) > LOGIN_THROTTLE_WINDOW) {
        return false;
    }
    return (int) $entry['count'] >= LOGIN_THROTTLE_LIMIT;
}

function recordLoginFailure(): void
{
    $ip = clientIp();
    $data = readLoginThrottle();
    $now = time();
    $entry = $data[$ip] ?? null;
    if ($entry === null || ($now - (int) $entry['first']) > LOGIN_THROTTLE_WINDOW) {
        $entry = ['count' => 0, 'first' => $now];
    }
    $entry['count'] = (int) $entry['count'] + 1;
    $entry['last'] = $now;
    $data[$ip] = $entry;
    writeLoginThrottle($data);
}

function clearLoginFailures(): void
{
    $data = readLoginThrottle();
    $ip = clientIp();
    if (isset($data[$ip])) {
        unset($data[$ip]);
        writeLoginThrottle($data);
    }
}

function verifyAdminCredentials(string $username, string $password): bool
{
    if (!adminCredentialsExist()) {
        return false;
    }
    $data = json_decode((string) file_get_contents(adminCredentialsFile()), true);
    if (!is_array($data) || !hash_equals((string) ($data['username'] ?? ''), $username)) {
        return false;
    }
    return password_verify($password, (string) ($data['password_hash'] ?? ''));
}

function startAdminSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('crcvinc_admin');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => isSecureRequest(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

/** HTTPS üzerinden mi geliniyor — doğrudan bağlantıda ve Cloudflare arkasında (X-Forwarded-Proto) çalışır. */
function isSecureRequest(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    return !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
        && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
}

function isAdminLoggedIn(): bool
{
    startAdminSession();
    return !empty($_SESSION['admin_authenticated']);
}

function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

/** POST isteklerinde CSRF token doğrular. */
function checkCsrf(): bool
{
    startAdminSession();
    $token = $_POST['csrf'] ?? '';
    return is_string($token) && hash_equals($_SESSION['csrf'] ?? '', $token);
}

function csrfField(): string
{
    startAdminSession();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}
