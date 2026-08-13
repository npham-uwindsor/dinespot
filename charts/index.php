<?php

$assetPrefix = '../';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'DineSpot Insights';
$pageDescription = 'Explore restaurant cuisine trends, reservation activity, and top-rated listings on DineSpot.';
$bodyClass = 'page-charts';

$cuisineData = [];
$reservationData = [];
$topRated = [];
$dbError = null;

try {
    if (database_is_ready()) {
        $cuisineData = get_restaurant_count_by_cuisine();
        $reservationData = get_reservation_count_by_status();
        $topRated = get_top_rated_restaurants(5);
    }
} catch (Throwable $e) {
    $dbError = 'Unable to load chart data.';
}

// JavaScript library for creating charts loaded via CDN (Traditional way)
$extraHead = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>DineSpot Insights</h1>
        <p class="lead">Live charts built from restaurants and reservations in the database.</p>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <?php if ($dbError): ?>
            <div class="alert alert-error" role="alert"><?= e($dbError) ?></div>
        <?php else: ?>
            <!-- Chart.js canvases; data passed via window.dineSpotCharts below -->
            <div class="charts-grid">
                <div class="content-card chart-card">
                    <h2>Restaurants by Cuisine</h2>
                    <canvas id="cuisine-chart" aria-label="Bar chart of restaurants by cuisine"></canvas>
                </div>
                <div class="content-card chart-card">
                    <h2>Reservations by Status</h2>
                    <canvas id="reservation-chart" aria-label="Doughnut chart of reservations by status"></canvas>
                </div>
            </div>

            <div class="content-card" style="margin-top: 1.5rem;">
                <h2>Top Rated Restaurants</h2>
                <?php if ($topRated === []): ?>
                    <p>No rated restaurants yet.</p>
                <?php else: ?>
                    <ul class="chart-ranking">
                        <?php foreach ($topRated as $restaurant): ?>
                            <li>
                                <strong><?= e($restaurant['name']) ?></strong>
                                <span><?= e(format_rating((float) $restaurant['avg_rating'])) ?> ★ (<?= (int) $restaurant['review_count'] ?> reviews)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <script>
                window.dineSpotCharts = {
                    cuisines: {
                        labels: <?= json_encode(array_column($cuisineData, 'cuisine'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                        values: <?= json_encode(array_map('intval', array_column($cuisineData, 'total')), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
                    },
                    reservations: {
                        labels: <?= json_encode(array_map(static fn ($row) => ucfirst($row['status']), $reservationData), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                        values: <?= json_encode(array_map('intval', array_column($reservationData, 'total')), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
                    }
                };
            </script>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
