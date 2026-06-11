<?php

$assetPrefix = '../';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login(client_path('login.php'));

$userId = (int) current_user_id();
$defaultRedirect = client_path('reservations.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $defaultRedirect);
    exit;
}

$reservationId = (int) ($_POST['id'] ?? 0);
$redirect = $_POST['redirect'] ?? $defaultRedirect;

if ($reservationId > 0) {
    cancel_reservation_by_id($reservationId, $userId);
}

$separator = str_contains($redirect, '?') ? '&' : '?';
header('Location: ' . $redirect . $separator . 'cancelled=1');
exit;
