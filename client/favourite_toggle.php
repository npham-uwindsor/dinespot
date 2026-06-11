<?php

$assetPrefix = '../';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login($assetPrefix . 'client/login.php');

$restaurantId = (int) ($_GET['restaurant_id'] ?? 0);
$redirect = $_GET['redirect'] ?? ($assetPrefix . 'restaurants/index.php');
$userId = current_user_id();

if ($restaurantId > 0) {
    if (is_restaurant_favourited($userId, $restaurantId)) {
        $stmt = db()->prepare(
            'DELETE FROM favourites WHERE user_id = :user_id AND restaurant_id = :restaurant_id'
        );
        $stmt->execute([
            'user_id' => $userId,
            'restaurant_id' => $restaurantId,
        ]);
    } else {
        $stmt = db()->prepare(
            'INSERT INTO favourites (user_id, restaurant_id) VALUES (:user_id, :restaurant_id)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'restaurant_id' => $restaurantId,
        ]);
    }
}

header('Location: ' . $redirect);
exit;
