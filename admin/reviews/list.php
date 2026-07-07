<?php

$assetPrefix = '../../';
$activeAdminPage = 'reviews';

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Reviews';
$pageDescription = 'Manage DineSpot reviews and their ratings.';
$bodyClass = 'page-client page-admin';

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$reviews = get_all_reviews_for_admin();

require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Manage Reviews</h1>
        <p class="lead">Moderate customer reviews across all restaurants.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
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
                        <h2>All Reviews</h2>
                        <p class="admin-table-meta"><?= count($reviews) ?> review<?= count($reviews) === 1 ? '' : 's' ?></p>
                    </div>
                </div>

                <?php if ($reviews === []): ?>
                    <p class="admin-table-empty">No reviews found.</p>
                <?php else: ?>
                    <div class="admin-table-scroll" tabindex="0" aria-label="Reviews table">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th scope="col">Restaurant</th>
                                    <th scope="col">Reviewer</th>
                                    <th scope="col">Rating</th>
                                    <th scope="col">Comment</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td class="admin-table-user-cell">
                                            <strong class="admin-table-user-name"><?= e($review['restaurant_name']) ?></strong>
                                            <span class="admin-table-user-id">#<?= (int) $review['id'] ?></span>
                                        </td>
                                        <td><?= e($review['user_name']) ?></td>
                                        <td><?= e(format_rating((float) $review['rating'])) ?> ★</td>
                                        <td><?= e($review['comment']) ?></td>
                                        <td><?= e(date('M j, Y', strtotime($review['created_at']))) ?></td>
                                        <td class="admin-table-actions">
                                            <a
                                                href="delete.php?id=<?= (int) $review['id'] ?>"
                                                class="btn btn-admin-action btn-suspend"
                                            >Delete</a>
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
