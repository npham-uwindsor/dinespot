<?php

$assetPrefix = '../';
$activeClientPage = 'reservations.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login($assetPrefix . 'client/login.php');

$pageTitle = 'My Reservations';
$pageDescription = 'View your DineSpot table reservations.';
$bodyClass = 'page-client';

$userId = current_user_id();
$stmt = db()->prepare(
    'SELECT r.id, r.reservation_date, r.reservation_time, r.party_size, r.status, r.notes,
            rest.name AS restaurant_name, rest.city, rest.province
     FROM reservations r
     INNER JOIN restaurants rest ON rest.id = r.restaurant_id
     WHERE r.user_id = :user_id
     ORDER BY r.reservation_date DESC, r.reservation_time DESC'
);
$stmt->execute(['user_id' => $userId]);
$reservations = $stmt->fetchAll();
$success = isset($_GET['cancelled']) ? 'Your reservation has been cancelled.' : '';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>My Reservations</h1>
        <p class="lead">Track pending, approved, and past bookings.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require __DIR__ . '/_sidebar.php'; ?>

        <div class="client-main">
            <?php if ($success !== ''): ?>
                <div class="alert alert-success" role="status"><?= e($success) ?></div>
            <?php endif; ?>
            <?php if ($reservations === []): ?>
                <div class="content-card">
                    <p>You have no reservations yet. <a href="<?= e($assetPrefix) ?>restaurants/index.php">Browse restaurants</a> to book a table.</p>
                </div>
            <?php else: ?>
                <div class="client-list">
                    <?php foreach ($reservations as $reservation): ?>
                        <article class="content-card client-list-item">
                            <div class="client-list-item-header">
                                <h2><?= e($reservation['restaurant_name']) ?></h2>
                                <span class="status-badge status-<?= e($reservation['status']) ?>"><?= e(ucfirst($reservation['status'])) ?></span>
                            </div>
                            <p><?= e($reservation['city']) ?>, <?= e($reservation['province']) ?></p>
                            <p>
                                <strong>Date:</strong> <?= e(date('F j, Y', strtotime($reservation['reservation_date']))) ?>
                                &middot;
                                <strong>Time:</strong> <?= e(date('g:i A', strtotime($reservation['reservation_time']))) ?>
                                &middot;
                                <strong>Party:</strong> <?= (int) $reservation['party_size'] ?>
                            </p>
                            <?php if (!empty($reservation['notes'])): ?>
                                <p><strong>Notes:</strong> <?= e($reservation['notes']) ?></p>
                            <?php endif; ?>
                            <?php if (in_array($reservation['status'], ['pending', 'approved'], true)): ?>
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-cancel-reservation
                                    data-reservation-id="<?= (int) $reservation['id'] ?>"
                                    data-restaurant-name="<?= e($reservation['restaurant_name']) ?>"
                                    data-reservation-date="<?= e(date('F j, Y', strtotime($reservation['reservation_date']))) ?>"
                                    data-reservation-time="<?= e(date('g:i A', strtotime($reservation['reservation_time']))) ?>"
                                    data-party-size="<?= (int) $reservation['party_size'] ?>"
                                    data-redirect="reservations.php"
                                    data-form-action="cancel_reservation.php"
                                >
                                    Cancel Reservation
                                </button>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/partials/confirm-modal.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
