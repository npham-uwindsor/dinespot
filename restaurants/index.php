<?php

$assetPrefix = '../';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Restaurants';
$pageDescription = 'Browse Canadian restaurants on DineSpot by cuisine, city, and rating.';
$bodyClass = 'page-restaurants';

$restaurants = [];
$dbError = null;

try {
    $restaurants = get_all_restaurants();
} catch (Throwable $e) {
    $dbError = 'Unable to load restaurants. Please check your database connection.';
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Browse Restaurants</h1>
        <p class="lead">Explore <?= count($restaurants) ?> restaurants across Canada — from casual favourites to celebrated dining rooms.</p>
        <a class="btn btn-secondary" href="search.php">Search &amp; Filter</a>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <?php
        $contextHelpIntro = 'Learn how to browse the restaurant catalogue and open listing details.';
        require __DIR__ . '/../includes/partials/context-help.php';
        ?>
        <?php if ($dbError): ?>
            <div class="alert alert-error" role="alert"><?= e($dbError) ?></div>
        <?php elseif ($restaurants === []): ?>
            <div class="content-card">
                <p>No restaurants available yet. Import the database seed file to get started.</p>
            </div>
        <?php else: ?>
            <div class="restaurant-grid">
                <?php foreach ($restaurants as $restaurant): ?>
                    <?php if ($restaurant['is_active'] == 1): ?>
                        <?php include __DIR__ . '/../includes/partials/restaurant-card.php'; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
