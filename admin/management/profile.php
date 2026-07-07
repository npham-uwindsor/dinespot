<?php

$assetPrefix = '../../';
$activeAdminPage = 'profile';

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Admin Account';
$pageDescription = 'Manage your DineSpot admin account details.';
$bodyClass = 'page-client page-admin';

$user = logged_in_user();
$success = '';
if (isset($_GET['updated'])) {
    $success = 'Your account has been updated.';
}

if (!$user) {
    logout_user();
    header('Location: ' . client_path('login.php'));
    exit;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Admin Account</h1>
        <p class="lead">Manage your administrator profile and site access.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require __DIR__ . '/_sidebar.php'; ?>

        <div class="client-main">
            <div class="content-card">
                <?php if ($success !== ''): ?>
                    <div class="alert alert-success" role="status"><?= e($success) ?></div>
                <?php endif; ?>

                <div class="profile-header">
                    <h2>Account Details</h2>
                    <a class="btn btn-primary" href="edit_profile.php">Edit</a>
                </div>

                <dl class="profile-details">
                    <div class="profile-detail">
                        <dt>Full Name</dt>
                        <dd><?= e($user['full_name']) ?></dd>
                    </div>
                    <div class="profile-detail">
                        <dt>Email Address</dt>
                        <dd><?= e($user['email']) ?></dd>
                    </div>
                    <div class="profile-detail">
                        <dt>Phone Number</dt>
                        <dd><?= $user['phone'] !== null && $user['phone'] !== '' ? e($user['phone']) : '—' ?></dd>
                    </div>
                    <div class="profile-detail">
                        <dt>Role</dt>
                        <dd><?= e(ucfirst($user['role'])) ?></dd>
                    </div>
                    <div class="profile-detail">
                        <dt>Member Since</dt>
                        <dd><?= e(date('F j, Y', strtotime($user['created_at']))) ?></dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
