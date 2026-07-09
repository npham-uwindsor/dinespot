<?php

$assetPrefix = '../';
$activeHelpPage = 'updating-content.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Help: Updating Content';
$pageDescription = 'Instructions for admins on adding restaurants, images, themes, and media to DineSpot.';
$bodyClass = 'page-help';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Updating Content</h1>
        <p class="lead">Simple instructions for keeping the restaurant catalogue and media files fresh.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require __DIR__ . '/_sidebar.php'; ?>

        <div class="client-main">
            <div class="content-card">
                <h2>Who can update content?</h2>
                <p>Only admin accounts can add or edit restaurants, change themes, and moderate reviews. Sign in at <a href="<?= e(client_path('login.php')) ?>">Sign In</a> with an admin email to open the <a href="<?= e(admin_path('dashboard.php')) ?>">Admin Dashboard</a>.</p>

                <h2>Add a new restaurant</h2>
                <ol>
                    <li>Go to <strong>Manage Restaurants</strong> and click <strong>Add Restaurant</strong>.</li>
                    <li>Fill in name, cuisine, city, province, description, address, and price range.</li>
                    <li>Upload a copyright-free JPG or PNG image (recommended size: at least 800 px wide).</li>
                    <li>Click save. The restaurant appears on the public browse and search pages.</li>
                </ol>

                <h2>Edit or remove listings</h2>
                <p>Open <a href="<?= e(admin_path('restaurants/list.php')) ?>">Manage Restaurants</a>, choose a listing, and update its details or menu items.</p>

                <h2>Update restaurant images</h2>
                <p>When adding a restaurant through the admin form, the file is uploaded automatically. To replace an image, upload a new file with the same filename to overwrite the current one or upload a new photo with a different name which is not existing.</p>

                <h2>Change the site theme</h2>
                <p>Admins can switch between Classic, Refresh, and Forest themes from <a href="<?= e(admin_path('theme/settings.php')) ?>">Theme Settings</a>. Preview a theme before saving it as the site default.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
