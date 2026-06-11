<?php

$assetPrefix = '../';
$activeClientPage = 'profile.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login(client_path('login.php'));

$pageTitle = 'Change Password';
$pageDescription = 'Update your DineSpot account password.';
$bodyClass = 'page-client';

$user = logged_in_user();
$error = '';

if (!$user) {
    logout_user();
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $error = 'Please fill in all password fields.';
    } elseif (!verify_password($currentPassword, $user['password_hash'])) {
        $error = 'Your current password is incorrect.';
    } elseif (strlen($newPassword) < 8) {
        $error = 'Your new password must be at least 8 characters.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match.';
    } else {
        update_user_password((int) $user['id'], $newPassword);
        header('Location: profile.php?password_updated=1');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Change Password</h1>
        <p class="lead">Choose a strong password to keep your account secure.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require __DIR__ . '/_sidebar.php'; ?>

        <div class="client-main">
            <div class="content-card">
                <?php if ($error !== ''): ?>
                    <div class="alert alert-error" role="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <form class="auth-form" method="post" action="change_password.php" novalidate>
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" minlength="8" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" minlength="8" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Password</button>
                        <a class="btn btn-secondary" href="profile.php">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
