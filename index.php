<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Home';
$pageDescription = 'Discover, review, and reserve the best restaurants across Canada with DineSpot.';
$bodyClass = 'page-home';

$featured = [];
$dbError = null;
$successMessage = $_GET['success'] ?? null;

try {
    if (database_is_ready()) {
        $featured = get_featured_restaurants(6);
    }
} catch (Throwable $e) {
    $dbError = 'Unable to load restaurants. Please check your database connection.';
}

$videoUrl = 'https://youtu.be/g4hiF5axjPc?si=lyX6YaIEcgZfqCEp';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero: headline, CTA buttons, and intro video -->
<section class="home-hero">
    <div class="container home-hero-grid">
        <div>
            <h1>Find your next favourite restaurant</h1>
            <p class="lead"><?= e(SITE_TAGLINE) ?></p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="restaurants/index.php">Browse Restaurants</a>
                <a class="btn btn-secondary" href="restaurants/search.php">Search</a>
            </div>
        </div>
        <?php if ($videoUrl !== ''): ?>
            <div class="hero-video">
                <iframe
                    src="<?= e(youtube_embed_url($videoUrl)) ?>"
                    title="DineSpot introduction video"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Featured restaurant grid (loaded from database) -->
<section class="page-content">
    <div class="container">
        <?php if ($successMessage): ?>
            <div class="alert alert-success" role="alert">
                <?= e($successMessage) ?>
            </div>
        <?php endif; ?>
        <?php if ($dbError): ?>
            <div class="alert alert-error" role="alert"><?= e($dbError) ?></div>
        <?php elseif ($featured === []): ?>
            <div class="alert alert-error" role="alert">
                No restaurants found. Import <code>schema/schema_mysql.sql</code> into MySQL via XAMPP.
            </div>
        <?php else: ?>
            <div class="section-heading">
                <h2>Featured Restaurants</h2>
                <a href="restaurants/index.php">View all</a>
            </div>
            <div class="restaurant-grid">
                <?php foreach ($featured as $restaurant): ?>
                    <?php include __DIR__ . '/includes/partials/restaurant-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
