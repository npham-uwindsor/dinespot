<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact';
$pageDescription = 'Get in touch with the DineSpot team for support, feedback, or partnership inquiries.';
$bodyClass = 'page-contact';

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Contact Us</h1>
        <p class="lead">Questions about reservations, your account, or partnering with DineSpot? Use the contact details below to reach our team.</p>
    </div>
</section>

<section class="page-content">
    <div class="container contact-centered">
        <div class="content-card contact-details">
            <h2>Get in Touch</h2>
            <p>
                <strong>Email</strong>
                <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a>
            </p>
            <p>
                <strong>Phone</strong>
                <?= e(SITE_PHONE) ?>
            </p>
            <p>
                <strong>Office</strong>
                <?= e(SITE_ADDRESS) ?>
            </p>
            <p>
                <strong>Support Hours</strong>
                Monday – Friday, 9:00 AM – 5:00 PM EST
            </p>
            <p>
                <strong>Reservation Issues</strong>
                For urgent same-day reservation changes, call us and have your reservation ID and restaurant name ready.
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
