<?php
declare(strict_types=1);

require dirname(__DIR__, 2) . '/app/bootstrap.php';
require APP_DIR . '/auth.php';

startAdminSession();
$_SESSION = [];
session_destroy();
header('Location: /admin/login.php');
