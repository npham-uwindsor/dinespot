<?php

$assetPrefix = '../';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login(client_path('login.php'));

$restaurantId = (int) ($_GET['restaurant_id'] ?? $_POST['restaurant_id'] ?? 0);
$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? ($assetPrefix . 'restaurants/index.php');
$userId = (int) current_user_id();
$restaurant = $restaurantId > 0 ? get_restaurant_by_id($restaurantId) : null;

if (!$restaurant) {
    header('Location: ' . $assetPrefix . 'restaurants/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationDate = trim($_POST['reservation_date'] ?? '');
    $reservationTime = trim($_POST['reservation_time'] ?? '');
    $partySize = (int) ($_POST['party_size'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    $error = '';

    if ($reservationDate === '' || $reservationTime === '' || $partySize < 1) {
        $error = 'Please choose a date, time, and party size.';
    } elseif ($partySize > 20) {
        $error = 'Party size must be between 1 and 20 guests.';
    } elseif (is_restaurant_reserved($userId, $restaurantId)) {
        $error = 'You already have an active reservation at this restaurant.';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $reservationDate);
        $today = new DateTime('today');

        if (!$date || $date < $today) {
            $error = 'Please choose a valid date today or in the future.';
        } else {
            $stmt = db()->prepare(
                'INSERT INTO reservations
                    (restaurant_id, user_id, reservation_date, reservation_time, party_size, status, notes)
                 VALUES
                    (:restaurant_id, :user_id, :reservation_date, :reservation_time, :party_size, \'pending\', :notes)'
            );
            $stmt->execute([
                'restaurant_id' => $restaurantId,
                'user_id' => $userId,
                'reservation_date' => $reservationDate,
                'reservation_time' => $reservationTime,
                'party_size' => $partySize,
                'notes' => $notes !== '' ? $notes : null,
            ]);

            header('Location: ' . $redirect);
            exit;
        }
    }
}

$showCancelConfirm = is_restaurant_reserved($userId, $restaurantId);
$activeReservation = $showCancelConfirm
    ? get_active_reservation_for_restaurant($userId, $restaurantId)
    : null;

$pageTitle = $showCancelConfirm ? 'Cancel Reservation' : 'Reserve a Table';
$pageDescription = $showCancelConfirm
    ? 'Confirm cancellation of your reservation at ' . $restaurant['name'] . '.'
    : 'Request a table reservation at ' . $restaurant['name'] . '.';
$bodyClass = 'page-reservation-modal';

$reservationDate = $reservationDate ?? '';
$reservationTime = $reservationTime ?? '';
$partySize = $partySize ?? 2;
$notes = $notes ?? '';
$error = $error ?? '';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="modal-overlay" role="presentation">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="reservation-modal-title">
        <?php if ($showCancelConfirm && !$activeReservation): ?>
            <h1 id="reservation-modal-title">Reservation Not Found</h1>
            <p class="modal-lead">We could not find an active reservation to cancel.</p>
            <a class="btn btn-secondary" href="<?= e($redirect) ?>">Back</a>
        <?php elseif ($showCancelConfirm && $activeReservation): ?>
            <h1 id="reservation-modal-title">Cancel Reservation?</h1>
            <p class="modal-lead">at <?= e($restaurant['name']) ?></p>
            <p>
                <strong>Date:</strong> <?= e(date('F j, Y', strtotime($activeReservation['reservation_date']))) ?>
                &middot;
                <strong>Time:</strong> <?= e(date('g:i A', strtotime($activeReservation['reservation_time']))) ?>
                &middot;
                <strong>Party:</strong> <?= (int) $activeReservation['party_size'] ?>
            </p>
            <p>Are you sure you want to cancel this reservation?</p>

            <form class="reservation-form" method="post" action="cancel_reservation.php">
                <input type="hidden" name="id" value="<?= (int) $activeReservation['id'] ?>">
                <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">Confirm Cancellation</button>
                    <a class="btn btn-secondary" href="<?= e($redirect) ?>">Keep Reservation</a>
                </div>
            </form>
        <?php else: ?>
        <h1 id="reservation-modal-title">Reserve a Table</h1>
        <p class="modal-lead">at <?= e($restaurant['name']) ?></p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="auth-form reservation-form" method="post" action="reservation_toggle.php" novalidate>
            <input type="hidden" name="restaurant_id" value="<?= $restaurantId ?>">
            <input type="hidden" name="redirect" value="<?= e($redirect) ?>">

            <div class="form-group">
                <label for="reservation_date">Date</label>
                <input
                    type="date"
                    id="reservation_date"
                    name="reservation_date"
                    value="<?= e($reservationDate) ?>"
                    min="<?= e(date('Y-m-d')) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="reservation_time">Time</label>
                <input
                    type="time"
                    id="reservation_time"
                    name="reservation_time"
                    value="<?= e($reservationTime) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="party_size">Party Size</label>
                <select id="party_size" name="party_size" required>
                    <?php for ($size = 1; $size <= 20; $size++): ?>
                        <option value="<?= $size ?>"<?= (int) $partySize === $size ? ' selected' : '' ?>>
                            <?= $size ?> <?= $size === 1 ? 'guest' : 'guests' ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="notes">Special Requests <span class="label-optional">(optional)</span></label>
                <textarea id="notes" name="notes" rows="3" placeholder="Window seat, dietary needs, celebration, etc."><?= e($notes) ?></textarea>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Confirm Reservation</button>
                <a class="btn btn-secondary" href="<?= e($redirect) ?>">Cancel</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>