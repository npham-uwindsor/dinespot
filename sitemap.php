<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="restaurants/index.php">Restaurants</a></li>
                    <li><a href="restaurants/search.php">Search</a></li>
                    <li><a href="guide.php">User Guide</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms of Service</a></li>
                </ul>
            </div>
            <div class="content-card">
                <h2>Account Pages</h2>
                <ul>
                    <li><a href="client/login.php">Sign In</a></li>
                    <li><a href="client/register.php">Create Account</a></li>
                    <li><a href="client/profile.php">My Account</a></li>
                    <li><a href="client/edit_profile.php">Edit Profile</a></li>
                    <li><a href="client/change_password.php">Change Password</a></li>
                    <li><a href="client/reservations.php">My Reservations</a></li>
                    <li><a href="client/favourites.php">My Favourites</a></li>
                    <li><a href="client/my_reviews.php">My Reviews</a></li>
                </ul>
            </div>
            <?php if (is_admin()): ?>
                <div class="content-card">
                    <h2>Admin Pages</h2>
                    <ul>
                        <li><a href="charts/index.php">Insights</a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
