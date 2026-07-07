<?php

if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/config.php';
}

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? SITE_NAME;
$bodyClass = $bodyClass ?? '';
$assetPrefix = $assetPrefix ?? ($GLOBALS['assetPrefix'] ?? '');
$GLOBALS['assetPrefix'] = $assetPrefix;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!--
    Author: Tuong Nguyen Pham
    Student ID: 110192780
    Date: TBD
    COMP 3340 - Web Development
    Couse Project
    HTML5, CSS, JS, PHP, MySQL
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?></title>
    <!-- SEO & social metadata -->
    <meta name="description" content="<?= e($pageDescription ?? SITE_TAGLINE) ?>">
    <meta name="keywords" content="DineSpot, Restaurant, Reservation, Review, Guide, FAQ, Contact">
    <meta name="author" content="Tuong Nguyen Pham">
    <meta name="robots" content="index, follow">
    <!-- External fonts & stylesheets (theme CSS loaded dynamically) -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=account_circle,logout,manage_accounts " />
    <link rel="stylesheet" href="<?= e($assetPrefix ?? '') ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= e($assetPrefix ?? '') ?>assets/css/<?= e(theme_stylesheet()) ?>">
    <link rel="icon" type="image/x-icon" href="<?= e($assetPrefix ?? '') ?>assets/images/DineSpot-logo-fav.ico">
    <!-- extra head content is inserted in the head section -->
    <?= $extraHead ?? '' ?>
</head>
<body class="<?= e($bodyClass) ?> theme-<?= e(get_active_theme()) ?>">
    <!-- Skip link for keyboard / screen-reader users -->
    <a class="skip-link" href="#main-content">Skip to content</a>

    <!-- Global site header with responsive navigation -->
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

            <!-- Main nav links; auth state controls Profile / Sign In buttons -->
            <nav id="primary-nav" class="primary-nav" aria-label="Main navigation">
                <a href="<?= e($assetPrefix ?? '') ?>index.php"<?= is_active_page('index.php') ? ' aria-current="page"' : '' ?>>Home</a>
                <a href="<?= e($assetPrefix ?? '') ?>restaurants/index.php"<?= is_active_page('restaurants') ? ' aria-current="page"' : '' ?>>Restaurants</a>
                <a href="<?= e($assetPrefix ?? '') ?>about.php"<?= is_active_page('about.php') ? ' aria-current="page"' : '' ?>>About</a>
                <a href="<?= e($assetPrefix ?? '') ?>faq.php"<?= is_active_page('faq.php') ? ' aria-current="page"' : '' ?>>FAQ</a>
                <a href="<?= e($assetPrefix ?? '') ?>guide.php"<?= is_active_page('guide.php') ? ' aria-current="page"' : '' ?>>Quick Guide</a>
                <a href="<?= e(context_help_path()) ?>"<?= is_active_page('help') ? ' aria-current="page"' : '' ?>>Help</a>
                <a href="<?= e($assetPrefix ?? '') ?>contact.php"<?= is_active_page('contact.php') ? ' aria-current="page"' : '' ?>>Contact</a>
                <?php if (is_logged_in()): ?>
                    <a href="<?= e($assetPrefix ?? '') ?>charts/index.php"<?= is_active_page('charts') ? ' aria-current="page"' : '' ?>>Insights</a>
                <?php endif; ?>
                <?php if (is_client()): ?>
                    <a class="nav-cta nav-cta-outline nav-cta-profile" href="<?= e(client_path('profile.php')) ?>">
                        <span class="material-symbols-outlined" aria-hidden="true">account_circle</span> Profile
                    </a>
                    <a class="nav-cta" href="<?= e(client_path('logout.php')) ?>">
                        <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                        Logout
                    </a>
                <?php elseif (is_admin()): ?>
                    <a class="nav-cta nav-cta-outline nav-cta-profile" href="<?= e(admin_path('dashboard.php'))?>">
                        <span class="material-symbols-outlined" aria-hidden="true">manage_accounts</span> Admin Dashboard
                    </a>
                    <a class="nav-cta" href="<?= e(client_path('logout.php')) ?>">
                        <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                        Logout
                    </a>
                <?php else: ?>
                    <a class="nav-cta" href="<?= e(client_path('login.php')) ?>">Sign In</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Page-specific content is inserted between header and footer -->
    <main id="main-content">
