<?php

$assetPrefix = '../';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login(client_path('login.php'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . client_path('my_reviews.php'));
    exit;
}

$reviewId = (int) ($_POST['id'] ?? 0);
$userId = (int) current_user_id();
$redirect = $_POST['redirect'] ?? client_path('my_reviews.php');

if ($reviewId > 0) {
    delete_review_by_id($reviewId, $userId);
}

$separator = str_contains($redirect, '?') ? '&' : '?';
header('Location: ' . $redirect . $separator . 'deleted=1');
exit;
