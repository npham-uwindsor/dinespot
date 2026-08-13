<?php

$assetPrefix = '../';
$activeHelpPage = 'index.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Help Wiki';
$pageDescription = 'DineSpot help wiki with guides for browsing restaurants, reservations, accounts, and updating site content.';
$bodyClass = 'page-help';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Help Wiki</h1>
        <p class="lead">Browse topic-based help pages for every major part of DineSpot.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require __DIR__ . '/_sidebar.php'; ?>

        <div class="client-main">
            <div class="content-card">
                <h2>Welcome to the DineSpot Help Wiki</h2>
                <p>This wiki explains how to use DineSpot as a diner and how to keep the restaurant catalogue up to date. Each page focuses on one topic so you can quickly find what you need.</p>

                <ul class="help-topic-list">
                    <li>
                        <a href="browsing.php"><strong>Browsing Restaurants</strong></a>
                        <span>Search listings, open restaurant pages, and use the meal cost calculator.</span>
                    </li>
                    <li>
                        <a href="reservations.php"><strong>Reservations</strong></a>
                        <span>Request a table, read the reservation estimate, and track booking status.</span>
                    </li>
                    <li>
                        <a href="account.php"><strong>Your Account</strong></a>
                        <span>Register, sign in, manage favourites, and write reviews.</span>
                    </li>
                    <li>
                        <a href="updating-content.php"><strong>Updating Content</strong></a>
                        <span>Add restaurants, upload images, change themes, and refresh media files.</span>
                    </li>
                </ul>

                <p style="margin-top: 1.5rem;">New to the site? Try the interactive <a href="<?= e(asset_prefix()) ?>guide.php">step-by-step guide</a> or read the <a href="<?= e(asset_prefix()) ?>faq.php">FAQ</a>.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
