<?php

$assetPrefix = '../';
$activeClientPage = 'profile.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login(client_path('login.php'));

$pageTitle = 'Edit Account';
$pageDescription = 'Update your DineSpot account details.';
$bodyClass = 'page-client';

$user = logged_in_user();
$error = '';

if (!$user) {
    logout_user();
    header('Location: login.php');
    exit;
}

$full_name = $user['full_name'];
$email = $user['email'];
$phone = $user['phone'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($full_name === '' || $email === '') {
        $error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($phone !== '' && !preg_match('/^\d{10}$/', $phone)) {
        $error = 'Please enter a valid phone number in the format (xxx) xxx-xxxx.';
    } else {
        $existing = get_user_by_email($email);
        if ($existing && (int) $existing['id'] !== (int) $user['id']) {
            $error = 'That email is already in use.';
        } else {
            $userData = [
                'full_name' => $full_name,
                'email' => $email,
                'status' => $user['status'],
                'phone' => $phone,
            ];
            update_user((int) $user['id'], $userData);
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;
            header('Location: profile.php?updated=1');
            exit;
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Edit Account</h1>
        <p class="lead">Update your name, email, and phone number.</p>
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

                <h2>Account Details</h2>
                <form class="auth-form" method="post" action="edit_profile.php" novalidate>
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?= e($full_name) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?= e($phone) ?>" placeholder="(xxx) xxx-xxxx">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a class="btn btn-secondary" href="profile.php">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
