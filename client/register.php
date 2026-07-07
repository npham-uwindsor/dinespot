<?php

$assetPrefix = '../';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Create Account';
$pageDescription = 'Create a new DineSpot account to book reservations, manage favourites, and leave reviews.';
$bodyClass = 'page-register';

$error = '';
$full_name = '';
$email = '';
$phone = '';

redirect_if_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($email === '' || $password === '' || $full_name === '' || $phone === '') {
        $error = 'Please enter your all the required fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $user = [
            'email' => $email,
            'password' => $password,
            'full_name' => $full_name,
            'role' => 'client',
            'status' => 'active',
            'phone' => $phone,
        ];
        try {
            create_user($user);
            header('Location: ' . $assetPrefix . 'client/login.php');
            exit;
        } catch (Exception $e) {
            $error = 'Failed to create account. Please try again.';
            error_log($e->getMessage());
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Create Account</h1>
        <p class="lead">Create a new DineSpot account to book reservations, manage favourites, and leave reviews.</p>
    </div>
</section>

<section class="page-content">
    <div class="container auth-centered">
        <div class="content-card auth-card">
            <?php if ($error !== ''): ?>
                <div class="alert alert-error" role="alert"><?= e($error) ?></div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="register.php" novalidate>
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?= e($full_name) ?>" autocomplete="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="new-password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?= e($phone) ?>" autocomplete="tel" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Create</button>
                </div>
                <div class="auth-footer-text">
                    Already have an account? <a href="<?= e($assetPrefix . 'client/login.php') ?>">Sign in</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>