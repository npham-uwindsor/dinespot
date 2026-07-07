<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Site Map';
$pageDescription = 'Browse all public and account pages on DineSpot.';
$bodyClass = 'page-sitemap';

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <h1>Site Map</h1>
        <p class="lead">Quick links to every major page on DineSpot.</p>
    </div>
</section>

<section class="page-content">
    <div class="container sitemap">
        <div class="content-grid two-col">
            <div class="content-card">
                <h2>Public Pages</h2>
                <ul>
                    <li><a href="<?= e(asset_prefix()) ?>index.php">Home</a></li>
                    <li><a href="<?= e(asset_prefix()) ?>restaurants/index.php">Restaurants</a></li>
                    <li><a href="<?= e(asset_prefix()) ?>restaurants/search.php">Search</a></li>
                    <li><a href="<?= e(asset_prefix()) ?>guide.php">Quick Guide</a></li>
                    <li><a href="<?= e(help_path()) ?>">Help Wiki</a></li>
                    <li><a href="<?= e(help_path('browsing.php')) ?>">Help: Browsing</a></li>
                    <li><a href="<?= e(help_path('reservations.php')) ?>">Help: Reservations</a></li>
                    <li><a href="<?= e(help_path('account.php')) ?>">Help: Account</a></li>
                    <li><a href="<?= e(help_path('updating-content.php')) ?>">Help: Updating Content</a></li>
                    <li><a href="<?= e(asset_prefix()) ?>about.php">About</a></li>
                    <li><a href="<?= e(asset_prefix()) ?>faq.php">FAQ</a></li>
                    <li><a href="<?= e(asset_prefix()) ?>contact.php">Contact</a></li>
                    <li><a href="<?= e(asset_prefix()) ?>privacy.php">Privacy Policy</a></li>
                    <li><a href="<?= e(asset_prefix()) ?>terms.php">Terms of Service</a></li>
                    <li><a href="<?= e(asset_prefix()) ?>sitemap.php">Site Map</a></li>
                </ul>
            </div>
            <div class="content-card">
                <h2>Account Pages</h2>
                <ul>
                    <li><a href="<?= e(client_path('login.php')) ?>">Sign In</a></li>
                    <li><a href="<?= e(client_path('register.php')) ?>">Create Account</a></li>
                    <li><a href="<?= e(client_path('profile.php')) ?>">My Account</a></li>
                    <li><a href="<?= e(client_path('edit_profile.php')) ?>">Edit Profile</a></li>
                    <li><a href="<?= e(client_path('change_password.php')) ?>">Change Password</a></li>
                    <li><a href="<?= e(client_path('reservations.php')) ?>">My Reservations</a></li>
                    <li><a href="<?= e(client_path('favourites.php')) ?>">My Favourites</a></li>
                    <li><a href="<?= e(client_path('my_reviews.php')) ?>">My Reviews</a></li>
                    <li><a href="<?= e(asset_prefix()) ?>charts/index.php">Insights</a></li>
                </ul>
            </div>
            <?php if (is_admin()): ?>
                <div class="content-card">
                    <h2>Admin Pages</h2>
                    <ul>
                        <li><a href="<?= e(admin_path('dashboard.php')) ?>">Dashboard</a></li>
                        <li><a href="<?= e(admin_path('management/profile.php')) ?>">My Account</a></li>
                        <li><a href="<?= e(admin_path('management/edit_profile.php')) ?>">Edit Profile</a></li>
                        <li><a href="<?= e(admin_path('users/list.php')) ?>">Manage Users</a></li>
                        <li><a href="<?= e(admin_path('restaurants/list.php')) ?>">Manage Restaurants</a></li>
                        <li><a href="<?= e(admin_path('restaurants/add.php')) ?>">Add Restaurant</a></li>
                        <li><a href="<?= e(admin_path('reservations/list.php')) ?>">Manage Reservations</a></li>
                        <li><a href="<?= e(admin_path('reviews/list.php')) ?>">Manage Reviews</a></li>
                        <li><a href="<?= e(asset_prefix()) ?>charts/index.php">Insights</a></li>
                        <li><a href="<?= e(admin_path('theme/settings.php')) ?>">Theme Settings</a></li>
                        <li><a href="<?= e(asset_prefix()) ?>monitor.php">System Monitoring</a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
