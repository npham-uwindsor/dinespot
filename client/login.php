<?php

$assetPrefix = '../';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Sign In';
$pageDescription = 'Sign in to your DineSpot account to manage reservations, favourites, and reviews.';
$bodyClass = 'page-login';

$redirect = $_GET['redirect'] ?? ($assetPrefix . 'index.php');
$email = '';
$error = '';

redirect_if_logged_in($redirect);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? $redirect;

    if ($email === '' || $password === '') {
        $error = 'Please enter your email and password.';
    } else {
        $result = authenticate($email, $password);
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            login_user($result['user']);
            header('Location: ' . $redirect);
            exit;
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Sign In</h1>
        <p class="lead">Access your reservations, favourites, and reviews.</p>
    </div>
</section>

<section class="page-content">
    <div class="container auth-centered">
        <div class="content-card auth-card">
            <?php if ($error !== ''): ?>
                <div class="alert alert-error" role="alert"><?= e($error) ?></div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="login.php<?= $redirect !== ($assetPrefix . 'index.php') ? '?redirect=' . urlencode($redirect) : '' ?>" novalidate>
                <input type="hidden" name="redirect" value="<?= e($redirect) ?>">

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= e($email) ?>"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
                </div>
                
                <!-- Forgot Password -->
                <div class="form-group">
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>

                <!-- Message for forgot password -->
                <div class="form-group">
                    <p class="forgot-password-message"></p>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>

            <p class="auth-footer-text">
                Don't have an account? <a href="register.php">Create one</a>
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>