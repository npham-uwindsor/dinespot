<?php

if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/config.php';
}

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? SITE_NAME;
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?></title>
    <meta name="description" content="<?= e($pageDescription ?? SITE_TAGLINE) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=account_circle,logout" />
    <link rel="stylesheet" href="<?= e($assetPrefix ?? '') ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= e($assetPrefix ?? '') ?>assets/css/<?= e(theme_stylesheet()) ?>">
    <?= $extraHead ?? '' ?>
</head>
<body class="<?= e($bodyClass) ?> theme-<?= e(get_active_theme()) ?>">
    <a class="skip-link" href="#main-content">Skip to content</a>

    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="<?= e($assetPrefix ?? '') ?>index.php">
                <img class="logo-mark" src="<?= e($assetPrefix ?? '') ?>assets/images/DineSpot-logo.jpg" alt="DineSpot Logo">
                <span class="logo-text"><?= e(SITE_NAME) ?></span>
            </a>

            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
                <span class="sr-only">Menu</span>
            </button>

            <nav id="primary-nav" class="primary-nav" aria-label="Main navigation">
                <a href="<?= e($assetPrefix ?? '') ?>index.php"<?= is_active_page('index.php') ? ' aria-current="page"' : '' ?>>Home</a>
                <a href="<?= e($assetPrefix ?? '') ?>restaurants/index.php"<?= is_active_page('restaurants') ? ' aria-current="page"' : '' ?>>Restaurants</a>
                <a href="<?= e($assetPrefix ?? '') ?>about.php"<?= is_active_page('about.php') ? ' aria-current="page"' : '' ?>>About</a>
                <a href="<?= e($assetPrefix ?? '') ?>faq.php"<?= is_active_page('faq.php') ? ' aria-current="page"' : '' ?>>FAQ</a>
                <a href="<?= e($assetPrefix ?? '') ?>guide.php"<?= is_active_page('guide.php') ? ' aria-current="page"' : '' ?>>Guide</a>
                <a href="<?= e($assetPrefix ?? '') ?>contact.php"<?= is_active_page('contact.php') ? ' aria-current="page"' : '' ?>>Contact</a>
                <?php if (is_admin()): ?>
                    <a href="<?= e($assetPrefix ?? '') ?>charts/index.php"<?= is_active_page('charts') ? ' aria-current="page"' : '' ?>>Insights</a>
                <?php endif; ?>
                <?php if (is_logged_in()): ?>
                    <a class="nav-cta nav-cta-outline nav-cta-profile" href="<?= e($assetPrefix ?? '') ?>client/profile.php">
                        <span class="material-symbols-outlined" aria-hidden="true">account_circle</span> Profile
                    </a>
                    <a class="nav-cta" href="<?= e($assetPrefix ?? '') ?>client/logout.php">
                        <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                        Logout
                    </a>
                <?php else: ?>
                    <a class="nav-cta" href="<?= e($assetPrefix ?? '') ?>client/login.php">Sign In</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main id="main-content">
