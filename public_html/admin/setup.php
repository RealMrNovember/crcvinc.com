<?php
declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/bootstrap.php';
require APP_DIR . '/auth.php';

// Kimlik dosyası zaten oluşturulmuşsa kurulum ekranı tekrar açılmaz.
if (adminCredentialsExist()) {
    header('Location: /admin/login.php');
    exit;
}

startAdminSession();
$error = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!checkCsrf()) {
        $error = 'Oturum süresi doldu, sayfayı yenileyip tekrar deneyin.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');

        if ($username === '') {
            $error = 'Kullanıcı adı boş olamaz.';
        } elseif (strlen($password) < 8) {
            $error = 'Şifre en az 8 karakter olmalı.';
        } elseif ($password !== $confirm) {
            $error = 'Şifreler eşleşmiyor.';
        } elseif (!saveAdminCredentials($username, $password)) {
            $error = 'Kaydedilemedi. data/ klasörüne yazma izni olduğundan emin olun.';
        } else {
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_user'] = $username;
            header('Location: /admin/');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Kurulumu — CRC Vinç</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-auth">
<div class="auth-card">
  <h1>Yönetim Paneli Kurulumu</h1>
  <p>Bu panel ilk kez açılıyor. Kalıcı giriş bilgilerinizi oluşturun.</p>
  <?php if ($error): ?><div class="auth-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <?= csrfField() ?>
    <label>Kullanıcı adı<input type="text" name="username" required autofocus></label>
    <label>Şifre<input type="password" name="password" required minlength="8"></label>
    <label>Şifre (tekrar)<input type="password" name="password_confirm" required minlength="8"></label>
    <button type="submit">Paneli Oluştur</button>
  </form>
</div>
</body>
</html>
