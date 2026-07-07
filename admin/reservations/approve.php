<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

require_admin();

$id = (int) ($_GET['id'] ?? 0);
$reservation = get_reservation_by_id($id);

if (!$reservation) {
    header('Location: list.php?error=Reservation not found.');
    exit;
}

if ($reservation['status'] !== 'pending') {
    header('Location: list.php?error=Only pending reservations can be approved.');
    exit;
}

update_reservation_status_admin($id, 'approved');
header('Location: list.php?success=Reservation has been approved.');
exit;
