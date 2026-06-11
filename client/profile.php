<?php

$assetPrefix = '../';
$activeClientPage = 'profile.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login(client_path('login.php'));

$pageTitle = 'My Account';
$pageDescription = 'Manage your DineSpot account details.';
$bodyClass = 'page-client';

$user = logged_in_user();
$success = '';
if (isset($_GET['updated'])) {
    $success = 'Your account has been updated.';
} elseif (isset($_GET['password_updated'])) {
    $success = 'Your password has been updated.';
}

if (!$user) {
    logout_user();
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Account Details</h1>
        <p class="lead">You can manage your account, reservations, favourites, and reviews here.</p>
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
                    <div class="form-actions">
                        <a class="btn btn-primary" href="edit_profile.php">Edit</a>
                        <a class="btn btn-secondary" href="change_password.php">Change Password</a>
                    </div>
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
                        <dt>Member Since</dt>
                        <dd><?= e(date('F j, Y', strtotime($user['created_at']))) ?></dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
