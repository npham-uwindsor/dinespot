<?php

$assetPrefix = '../../';
$activeAdminPage = 'reservations';

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Reservations';
$pageDescription = 'Manage DineSpot reservations and their status.';
$bodyClass = 'page-client page-admin';

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$reservations = get_all_reservations_for_admin();

require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Manage Reservations</h1>
        <p class="lead">Review and update reservation requests.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout admin-reservations-card">
        <?php require admin_path('management/_sidebar.php'); ?>

        <div class="client-main">
            <?php if ($success !== ''): ?>
                <div class="alert alert-success" role="status"><?= e($success) ?></div>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <div class="alert alert-error" role="status"><?= e($error) ?></div>
            <?php endif; ?>
            <div class="content-card admin-users-card">
                <div class="admin-table-toolbar">
                    <div>
                        <h2>All Reservations</h2>
                        <p class="admin-table-meta"><?= count($reservations) ?> reservation<?= count($reservations) === 1 ? '' : 's' ?></p>
                    </div>
                </div>

                <?php if ($reservations === []): ?>
                    <p class="admin-table-empty">No reservations found.</p>
                <?php else: ?>
                    <div class="admin-table-scroll" tabindex="0" aria-label="Reservations table">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th scope="col">Restaurant</th>
                                    <th scope="col">Guest</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Party</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <tr>
                                        <td class="admin-table-user-cell">
                                            <strong class="admin-table-user-name"><?= e($reservation['restaurant_name']) ?></strong>
                                            <span class="admin-table-user-id">#<?= (int) $reservation['id'] ?></span>
                                        </td>
                                        <td><?= e($reservation['user_name']) ?></td>
                                        <td><?= e(date('M j, Y', strtotime($reservation['reservation_date']))) ?></td>
                                        <td><?= e(date('g:i A', strtotime($reservation['reservation_time']))) ?></td>
                                        <td><?= (int) $reservation['party_size'] ?></td>
                                        <td>
                                            <span class="status-badge status-<?= e($reservation['status']) ?>">
                                                <span class="status-dot" aria-hidden="true"></span>
                                                <?= e(ucfirst($reservation['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="admin-table-actions">
                                            <?php if ($reservation['status'] === 'pending'): ?>
                                                <a
                                                    href="approve.php?id=<?= (int) $reservation['id'] ?>"
                                                    class="btn btn-admin-action btn-unsuspend"
                                                >Approve</a>
                                                <a
                                                    href="reject.php?id=<?= (int) $reservation['id'] ?>"
                                                    class="btn btn-admin-action btn-suspend"
                                                >Reject</a>
                                            <?php elseif ($reservation['status'] === 'approved'): ?>
                                                <a
                                                    href="cancel.php?id=<?= (int) $reservation['id'] ?>"
                                                    class="btn btn-admin-action btn-suspend"
                                                >Cancel</a>
                                            <?php else: ?>
                                                <span>—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
