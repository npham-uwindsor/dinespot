<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'About Us';
$pageDescription = 'Learn how DineSpot helps diners discover restaurants, read reviews, and book tables across Canada.';
$bodyClass = 'page-about';

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>About DineSpot</h1>
        <p class="lead">We connect hungry Canadians with great dining experiences — from neighbourhood gems to celebrated destinations.</p>
    </div>
</section>

<section class="page-content">
    <div class="container content-grid two-col">
        <div class="content-card">
            <h2>Our Mission</h2>
            <p>DineSpot was built to make restaurant discovery simple, transparent, and personal. Whether you are planning a casual weeknight dinner or a special celebration, we help you find the right place, understand what to expect, and reserve a table with confidence.</p>
            <p>We focus on Canadian restaurants and cuisines, highlighting local favourites alongside nationally recognized dining rooms from coast to coast.</p>
        </div>

        <div class="content-card">
            <h2>What You Can Do</h2>
            <ul class="feature-list">
                <li>
                    <strong>Browse &amp; Search</strong>
                    Explore restaurants by cuisine, city, and rating to find your next meal.
                </li>
                <li>
                    <strong>Read &amp; Write Reviews</strong>
                    Share honest feedback and learn from other diners before you visit.
                </li>
                <li>
                    <strong>Reserve Tables</strong>
                    Request reservations online and track approval status from your account.
                </li>
                <li>
                    <strong>Save Favourites</strong>
                    Build a personal list of restaurants you want to try or visit again.
                </li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="stats-row">
            <div class="stat-box">
                <strong>10+</strong>
                <span>Cuisine categories</span>
            </div>
            <div class="stat-box">
                <strong>Canada-wide</strong>
                <span>Restaurant coverage</span>
            </div>
            <div class="stat-box">
                <strong>3+ users</strong>
                <span>Reviews, reservations &amp; favourites</span>
            </div>
        </div>

        <div class="content-card" style="margin-top: 1.5rem;">
            <h2>Built for Diners &amp; Restaurants</h2>
            <p>DineSpot serves two communities. Diners get a single place to research, review, and book. Restaurant partners benefit from increased visibility and a streamlined reservation workflow managed through our admin tools.</p>
            <p>Have questions about how the platform works? Visit our <a href="faq.php">FAQ</a> or <a href="contact.php">contact us</a> — we are happy to help.</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
