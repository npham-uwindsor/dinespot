<?php

$assetPrefix = '../../';
$activeAdminPage = 'users';

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Users';
$pageDescription = 'Manage DineSpot users and their permissions.';
$bodyClass = 'page-client page-admin';

$success = '';
$error = '';
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$users = get_all_users();

require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Manage Users</h1>
        <p class="lead">
            Manage users and their permissions.
        </p>
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
                        <h2>All Users</h2>
                        <p class="admin-table-meta"><?= count($users) ?> registered account<?= count($users) === 1 ? '' : 's' ?></p>
                    </div>
                </div>

                <?php if ($users === []): ?>
                    <p class="admin-table-empty">No users found.</p>
                <?php else: ?>
                    <div class="admin-table-scroll" tabindex="0" aria-label="User accounts table">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th scope="col">User</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <?php
                                    $isActive = is_account_active((int) $user['id']);
                                    $initials = strtoupper(substr($user['full_name'], 0, 1) . substr(strstr($user['full_name'], ' ') ?: '', 1, 1));
                                    if (strlen($initials) < 2) {
                                        $initials = strtoupper(substr($user['full_name'], 0, 2));
                                    }
                                    ?>
                                    <tr class="<?= $isActive ? '' : 'is-suspended' ?>">
                                        <td class="admin-table-user-cell">
                                            <strong class="admin-table-user-name"><?= e($user['full_name']) ?></strong>
                                            <span class="admin-table-user-email"><?= e($user['email']) ?></span>
                                            <span class="admin-table-user-id">#<?= (int) $user['id'] ?></span>
                                        </td>
                                        <td>
                                            <span class="role-badge role-<?= e($user['role']) ?>"><?= e(ucfirst($user['role'])) ?></span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?= e($user['status']) ?>">
                                                <span class="status-dot" aria-hidden="true"></span>
                                                <?= e(ucfirst($user['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="admin-table-phone"><?= e($user['phone'] ?: '—') ?></td>
                                        <td class="admin-table-actions">
                                            <?php if ($isActive && $user['role'] !== 'admin'): ?>
                                                <a
                                                    href="<?= e(admin_path('users/suspend.php?id=' . (int) $user['id'])) ?>"
                                                    class="btn btn-admin-action btn-suspend"
                                                >Suspend</a>
                                            <?php elseif ($user['role'] === 'admin'): ?>
                                                <span></span>
                                            <?php else: ?>
                                                <a
                                                    href="<?= e(admin_path('users/unsuspend.php?id=' . (int) $user['id'])) ?>"
                                                    class="btn btn-admin-action btn-unsuspend"
                                                >Restore</a>
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