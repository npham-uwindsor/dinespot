<?php

$assetPrefix = '../';
$activeClientPage = 'favourites.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login($assetPrefix . 'client/login.php');

$pageTitle = 'My Favourites';
$pageDescription = 'Your saved restaurants on DineSpot.';
$bodyClass = 'page-client';

$userId = current_user_id();
$stmt = db()->prepare(
    'SELECT rest.*
     FROM favourites f
     INNER JOIN restaurants rest ON rest.id = f.restaurant_id
     WHERE f.user_id = :user_id
     ORDER BY rest.name ASC'
);
$stmt->execute(['user_id' => $userId]);
$favourites = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>My Favourites</h1>
        <p class="lead">Restaurants you have saved for later.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require __DIR__ . '/_sidebar.php'; ?>

        <div class="client-main">
            <?php if ($favourites === []): ?>
                <div class="content-card">
                    <p>You have not saved any favourites yet. <a href="<?= e($assetPrefix) ?>restaurants/index.php">Explore restaurants</a> to build your list.</p>
                </div>
            <?php else: ?>
                <div class="restaurant-grid restaurant-grid-compact">
                    <?php foreach ($favourites as $restaurant): ?>
                        <?php
                        $restaurant['avg_rating'] = 0;
                        $restaurant['review_count'] = 0;
                        include __DIR__ . '/../includes/partials/restaurant-card.php';
                        ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
