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
        session_start();
    }
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
