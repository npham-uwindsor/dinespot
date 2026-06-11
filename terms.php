<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Terms of Service';
$pageDescription = 'Terms and conditions for using the DineSpot restaurant discovery and reservation platform.';
$bodyClass = 'page-terms';

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Terms of Service</h1>
        <p class="lead">Please read these terms carefully before using DineSpot.</p>
        <p><small>Last updated: June 11, 2026</small></p>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <div class="content-card legal-content">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing or using DineSpot, you agree to be bound by these Terms of Service and our <a href="privacy.php">Privacy Policy</a>. If you do not agree, please do not use the platform.</p>

            <h2>2. Description of Service</h2>
            <p>DineSpot provides an online platform for discovering restaurants, reading reviews, managing favourites, and submitting reservation requests. DineSpot facilitates communication between diners and restaurants but does not own or operate the restaurants listed on the site.</p>

            <h2>3. User Accounts</h2>
            <p>You are responsible for maintaining the confidentiality of your login credentials and for all activity under your account. You agree to provide accurate registration information and to update it when it changes. We may suspend or terminate accounts that violate these terms.</p>

            <h2>4. Reservations</h2>
            <p>Reservation requests submitted through DineSpot are subject to restaurant approval. An approved status on DineSpot indicates the restaurant has accepted your request, but restaurants may contact you directly regarding changes, cancellations, or seating details. DineSpot is not responsible for restaurant no-shows, closures, or service quality.</p>

            <h2>5. Reviews &amp; User Content</h2>
            <p>When you post a review or other content, you grant DineSpot a non-exclusive license to display and moderate that content on the platform. You agree not to post content that is false, defamatory, harassing, infringing, or otherwise unlawful. We reserve the right to remove content at our discretion.</p>

            <h2>6. Acceptable Use</h2>
            <p>You agree not to:</p>
            <ul>
                <li>Use the platform for fraudulent or unauthorized purposes</li>
                <li>Attempt to access accounts or data that do not belong to you</li>
                <li>Interfere with the security or operation of DineSpot</li>
                <li>Scrape, copy, or redistribute site content without permission</li>
            </ul>

            <h2>7. Restaurant Listings</h2>
            <p>Restaurant information, menus, images, and availability are provided by restaurants or third-party sources and may change without notice. DineSpot does not guarantee the accuracy of all listing details.</p>

            <h2>8. Disclaimer of Warranties</h2>
            <p>DineSpot is provided on an "as is" and "as available" basis. We make no warranties, express or implied, regarding uninterrupted access, accuracy, or fitness for a particular purpose.</p>

            <h2>9. Limitation of Liability</h2>
            <p>To the fullest extent permitted by law, DineSpot and its operators shall not be liable for any indirect, incidental, or consequential damages arising from your use of the platform, including issues related to dining experiences, reservation disputes, or third-party restaurant conduct.</p>

            <h2>10. Changes to These Terms</h2>
            <p>We may modify these Terms of Service at any time. Continued use of DineSpot after changes are posted constitutes acceptance of the revised terms.</p>

            <h2>11. Governing Law</h2>
            <p>These terms are governed by the laws of the Province of Ontario and the federal laws of Canada applicable therein, without regard to conflict of law principles.</p>

            <h2>12. Contact</h2>
            <p>Questions about these terms can be sent to <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a>.</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
