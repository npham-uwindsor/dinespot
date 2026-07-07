<?php

$assetPrefix = '../';
$activeHelpPage = 'reservations.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Help: Reservations';
$pageDescription = 'Learn how to request, estimate, and manage table reservations on DineSpot.';
$bodyClass = 'page-help';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Reservations</h1>
        <p class="lead">Request a table, review the live estimate, and track approval status.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require __DIR__ . '/_sidebar.php'; ?>

        <div class="client-main">
            <div class="content-card">
                <p>Listen to a short explanation of this help topic.</p>
                <audio controls>
                    <source src="<?= e(asset_prefix()) ?>assets/media/reservation-help.mp3" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
                <h2>Before you reserve</h2>
                <p>You need a DineSpot account to request a reservation. Browse to a restaurant page and click <strong>Reserve a Table</strong>.</p>

                <h2>Fill out the reservation form</h2>
                <ol>
                    <li>Choose a date and time for your visit.</li>
                    <li>Select your party size (1 to 20 guests).</li>
                    <li>Optionally pick an occasion such as birthday or anniversary.</li>
                    <li>Add special requests in the notes field if needed.</li>
                </ol>

                <h2>Read the reservation estimate</h2>
                <p>The form includes a live <strong>Reservation Estimate</strong> panel. As you change party size, date, time, or occasion, the page updates:</p>
                <ul>
                    <li><strong>Estimated deposit</strong> — $0 for small parties, $20 for 5–8 guests, $50 for 9 or more.</li>
                    <li><strong>Demand level</strong> — higher on weekends and during dinner rush (6–8 PM).</li>
                    <li><strong>Booking note</strong> — reminders about availability and celebration details.</li>
                </ul>

                <h2>Track your booking</h2>
                <p>After submitting, open <a href="<?= e(client_path('reservations.php')) ?>">My Reservations</a> to see whether your request is pending, approved, rejected, or cancelled.</p>

                <h2>Cancel a reservation</h2>
                <p>Pending reservations can be cancelled from your account or from the restaurant page. Approved reservations require a phone call to <?= e(SITE_PHONE) ?>.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
