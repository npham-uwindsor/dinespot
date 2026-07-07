<?php

$assetPrefix = '../../';
$activeAdminPage = 'restaurants';

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Restaurants';
$pageDescription = 'View DineSpot restaurant listings.';
$bodyClass = 'page-client page-admin';

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$restaurants = get_all_restaurants_for_admin();

require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Manage Restaurants</h1>
        <p class="lead">Browse all restaurant listings on DineSpot.</p>
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
                <?php
                $contextHelpIntro = 'Instructions for editing listings, images, and keeping the catalogue current.';
                require __DIR__ . '/../../includes/partials/context-help.php';
                ?>
                <div class="admin-table-toolbar">
                    <div>
                        <h2>All Restaurants</h2>
                        <p class="admin-table-meta"><?= count($restaurants) ?> listing<?= count($restaurants) === 1 ? '' : 's' ?></p>
                    </div>
                    <a href="add.php" class="btn btn-primary">Add Restaurant</a>
                </div>

                <?php if ($restaurants === []): ?>
                    <p class="admin-table-empty">No restaurants found.</p>
                <?php else: ?>
                    <div class="admin-table-scroll" tabindex="0" aria-label="Restaurants table">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th scope="col">Restaurant</th>
                                    <th scope="col">Cuisine</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Rating</th>
                                    <th scope="col">Active</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($restaurants as $restaurant): ?>
                                    <tr>
                                    <a href="edit.php?id=<?= $restaurant['id'] ?>">
                                        <td class="admin-table-user-cell">
                                            <strong class="admin-table-user-name"><?= e($restaurant['name']) ?></strong>
                                            <span class="admin-table-user-id">#<?= (int) $restaurant['id'] ?></span>
                                        </td>
                                        <td><?= e($restaurant['cuisine']) ?></td>
                                        <td><?= e($restaurant['city']) ?>, <?= e($restaurant['province']) ?></td>
                                        <td><?= e(price_range_label((int) $restaurant['price_range'])) ?></td>
                                        <td>
                                            <?php if ((int) $restaurant['review_count'] > 0): ?>
                                                <?= e(format_rating((float) $restaurant['avg_rating'])) ?> ★
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($restaurant['is_active']): ?>
                                                <span class="status-badge status-active">Yes</span>
                                            <?php else: ?>
                                                <span class="status-badge status-inactive">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit.php?id=<?= $restaurant['id'] ?>" class="btn btn-primary">Edit</a>
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
