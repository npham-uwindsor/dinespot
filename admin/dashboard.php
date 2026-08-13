<?php

$assetPrefix = '../';
$activeAdminPage = 'dashboard';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_admin();

$pageTitle = 'Dashboard';
$pageDescription = 'Overview of users, restaurants, reservations, and reviews on DineSpot.';
$bodyClass = 'page-client page-admin';

$user = logged_in_user();
$stats = [];
$recentReservations = [];
$recentReviews = [];
$dbError = null;

try {
    if (database_is_ready()) {
        $stats = get_admin_dashboard_stats();
        $recentReservations = get_recent_reservations_for_admin(5);
        $recentReviews = get_recent_reviews_for_admin(5);
    }
} catch (Throwable $e) {
    $dbError = 'Unable to load dashboard data. Please check your database connection.';
}

$activeTheme = theme_label(get_site_theme());
$siteVersion = get_site_setting('site_version', '1.0.0');

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Dashboard</h1>
        <p class="lead">
            Welcome back<?= $user ? ', ' . e($user['full_name']) : '' ?>. Here is a snapshot of site activity.
        </p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require admin_path('management/_sidebar.php'); ?>

        <div class="client-main">
            <?php if ($dbError): ?>
                <div class="alert alert-error" role="alert"><?= e($dbError) ?></div>
            <?php else: ?>
                <div class="admin-stats-row" aria-label="Site statistics">
                    <div class="stat-box">
                        <strong><?= (int) $stats['clients'] ?></strong>
                        <span class="admin-stat-label">Registered clients</span>
                    </div>
                    <div class="stat-box">
                        <strong><?= (int) $stats['active_restaurants'] ?></strong>
                        <span class="admin-stat-label">Active restaurants</span>
                    </div>
                    <div class="stat-box">
                        <strong><?= (int) $stats['reservations'] ?></strong>
                        <span class="admin-stat-label">Total reservations</span>
                    </div>
                    <div class="stat-box admin-stat-highlight">
                        <strong><?= (int) $stats['pending_reservations'] ?></strong>
                        <span class="admin-stat-label">Pending approval</span>
                    </div>
                    <div class="stat-box">
                        <strong><?= (int) $stats['reviews'] ?></strong>
                        <span class="admin-stat-label">Published reviews</span>
                    </div>
                </div>

                <div class="content-card">
                    <h2>Quick Actions</h2>
                    <div class="admin-quick-actions">
                        <a class="btn btn-secondary" href="<?= e(admin_path('users/list.php')) ?>">Manage Users</a>
                        <a class="btn btn-secondary" href="<?= e(admin_path('restaurants/list.php')) ?>">Manage Restaurants</a>
                        <a class="btn btn-secondary" href="<?= e(admin_path('reservations/list.php')) ?>">Manage Reservations</a>
                        <a class="btn btn-secondary" href="<?= e(admin_path('reviews/list.php')) ?>">Manage Reviews</a>
                        <a class="btn btn-secondary" href="<?= e(admin_path('theme/settings.php')) ?>">Theme Settings</a>
                        <a class="btn btn-secondary" href="<?= e($assetPrefix) ?>charts/index.php">View Insights</a>
                        <a class="btn btn-secondary" href="<?= e($assetPrefix) ?>monitor.php">System Monitoring</a>
                    </div>
                </div>

                <div class="admin-dashboard-grid">
                    <div class="content-card">
                        <div class="admin-panel-header">
                            <h2>Recent Reservations</h2>
                            <a href="<?= e(admin_path('reservations/list.php')) ?>">View all</a>
                        </div>
                        <?php if ($recentReservations === []): ?>
                            <p>No reservations yet.</p>
                        <?php else: ?>
                            <ul class="admin-activity-list">
                                <?php foreach ($recentReservations as $reservation): ?>
                                    <li>
                                        <div class="admin-activity-item">
                                            <div>
                                                <strong><?= e($reservation['restaurant_name']) ?></strong>
                                                <p>
                                                    <?= e($reservation['user_name']) ?>
                                                    &middot;
                                                    <?= e(date('M j, Y', strtotime($reservation['reservation_date']))) ?>
                                                    at <?= e(date('g:i A', strtotime($reservation['reservation_time']))) ?>
                                                    &middot;
                                                    Party of <?= (int) $reservation['party_size'] ?>
                                                </p>
                                            </div>
                                            <span class="status-badge status-<?= e($reservation['status']) ?>"><?= e(ucfirst($reservation['status'])) ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <div class="content-card">
                        <div class="admin-panel-header">
                            <h2>Recent Reviews</h2>
                            <a href="<?= e(admin_path('reviews/list.php')) ?>">View all</a>
                        </div>
                        <?php if ($recentReviews === []): ?>
                            <p>No reviews yet.</p>
                        <?php else: ?>
                            <ul class="admin-activity-list">
                                <?php foreach ($recentReviews as $review): ?>
                                    <li>
                                        <div class="admin-activity-item admin-activity-item-review">
                                            <div>
                                                <strong><?= e($review['restaurant_name']) ?></strong>
                                                <span class="admin-review-rating"><?= e(format_rating((float) $review['rating'])) ?> ★</span>
                                                <p><?= e($review['user_name']) ?> &middot; <?= e(date('M j, Y', strtotime($review['created_at']))) ?></p>
                                                <p class="admin-review-comment"><?= e($review['comment']) ?></p>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="content-card admin-site-summary">
                    <h2>Site Summary</h2>
                    <dl class="profile-details admin-summary-details">
                        <div class="profile-detail">
                            <dt>Total users</dt>
                            <dd><?= (int) $stats['users'] ?> (<?= (int) $stats['clients'] ?> clients)</dd>
                        </div>
                        <div class="profile-detail">
                            <dt>Restaurants listed</dt>
                            <dd><?= (int) $stats['restaurants'] ?> (<?= (int) $stats['active_restaurants'] ?> active)</dd>
                        </div>
                        <div class="profile-detail">
                            <dt>Average review rating</dt>
                            <dd><?= e(format_rating((float) $stats['avg_rating'])) ?> ★</dd>
                        </div>
                        <div class="profile-detail">
                            <dt>Saved favourites</dt>
                            <dd><?= (int) $stats['favourites'] ?></dd>
                        </div>
                        <div class="profile-detail">
                            <dt>Active theme</dt>
                            <dd><?= e($activeTheme) ?></dd>
                        </div>
                        <div class="profile-detail">
                            <dt>Site version</dt>
                            <dd><?= e($siteVersion) ?></dd>
                        </div>
                    </dl>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
