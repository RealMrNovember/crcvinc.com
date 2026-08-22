<?php
declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/bootstrap.php';
require APP_DIR . '/auth.php';

if (!adminCredentialsExist()) {
    header('Location: /admin/setup.php');
    exit;
}

startAdminSession();

if (isAdminLoggedIn()) {
    header('Location: /admin/');
    exit;
}

$error = null;
$attempts = &$_SESSION['login_attempts'];
$attempts ??= 0;
$lastAttempt = &$_SESSION['login_last_attempt'];
$lastAttempt ??= 0;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    // Session bazlı hızlı yavaşlatma + IP bazlı kalıcı sayaç (çerezini silen saldırgana karşı).
    if ($attempts >= 5 && (time() - $lastAttempt) < 30) {
        $error = 'Çok fazla deneme yapıldı. Lütfen 30 saniye sonra tekrar deneyin.';
    } elseif (isIpThrottled()) {
        $error = 'Bu IP adresinden çok fazla başarısız deneme yapıldı. Lütfen 15 dakika sonra tekrar deneyin.';
    } elseif (!checkCsrf()) {
        $error = 'Oturum süresi doldu, sayfayı yenileyip tekrar deneyin.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if (verifyAdminCredentials($username, $password)) {
            session_regenerate_id(true);
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_user'] = $username;
            $attempts = 0;
            clearLoginFailures();
            header('Location: /admin/');
            exit;
        }
        $attempts++;
        $lastAttempt = time();
        recordLoginFailure();
        $error = 'Kullanıcı adı veya şifre hatalı.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Girişi — CRC Vinç</title>
<link rel="stylesheet" href="<?= e(assetUrl('/assets/css/admin.css')) ?>">
</head>
<body class="admin-auth">
<div class="auth-card">
  <h1>Yönetim Paneli</h1>
  <?php if ($error): ?><div class="auth-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <?= csrfField() ?>
    <label>Kullanıcı adı<input type="text" name="username" required autofocus></label>
    <label>Şifre<input type="password" name="password" required></label>
    <button type="submit">Giriş Yap</button>
  </form>
</div>
</body>
</html>
