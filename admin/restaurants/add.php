<?php

$assetPrefix = '../../';
$activeAdminPage = 'restaurants';

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Add Restaurant';
$pageDescription = 'Add a new restaurant listing to DineSpot.';
$bodyClass = 'page-client page-admin';

$error = '';
$name = '';
$cuisine = '';
$city = '';
$province = '';
$description = '';
$address = '';
$image_path = '';
$price_range = 2;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $cuisine = trim($_POST['cuisine'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $province = strtoupper(trim($_POST['province'] ?? ''));
    $description = trim($_POST['description'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $image = $_FILES['image'] ?? null;
    $price_range = (int) ($_POST['price_range'] ?? 2);

    if ($name === '' || $cuisine === '' || $city === '' || $province === '' || $description === '') {
        $error = 'Name, cuisine, city, province, and description are required.';
    } elseif ($price_range < 1 || $price_range > 4) {
        $error = 'Please select a valid price range.';
    } elseif ($image === null) {
        $error = 'Image is required.';
    } else {
        $imageResult = image_upload('image', 'restaurants');
        if (is_array($imageResult) && isset($imageResult['error'])) {
            $error = $imageResult['error'];
        } else {
            $image_path = $imageResult['path'] ?? null;
        }
    }
    if ($error === '' && $image_path !== null) {
        $created = create_restaurant([
            'name' => $name,
            'cuisine' => $cuisine,
            'city' => $city,
            'province' => $province,
            'description' => $description,
            'address' => $address,
            'image_path' => $image_path,
            'price_range' => $price_range,
            'is_active' => 1,
        ]);

        if ($created) {
            header('Location: list.php?success=Restaurant has been added.');
            exit;
        }

        $error = 'Failed to add restaurant. Please try again.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Add Restaurant</h1>
        <p class="lead">Create a new restaurant listing.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require admin_path('management/_sidebar.php'); ?>

        <div class="client-main">
            <div class="content-card">
                <?php
                $contextHelpIntro = 'Step-by-step instructions for adding restaurants and uploading images.';
                require __DIR__ . '/../../includes/partials/context-help.php';
                ?>
                <?php if ($error !== ''): ?>
                    <div class="alert alert-error" role="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <h2>Restaurant Details</h2>
                <form class="auth-form" method="post" action="add.php" enctype="multipart/form-data" novalidate>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?= e($name) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="cuisine">Cuisine</label>
                        <input type="text" id="cuisine" name="cuisine" value="<?= e($cuisine) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?= e($city) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="province">Province</label>
                        <input type="text" id="province" name="province" value="<?= e($province) ?>" maxlength="10" placeholder="ON" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required><?= e($description) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" value="<?= e($address) ?>">
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/jpg" required>
                    </div>
                    <div class="form-group">
                        <label for="price_range">Price Range</label>
                        <select id="price_range" name="price_range" required>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <option value="<?= $i ?>"<?= $price_range === $i ? ' selected' : '' ?>><?= e(price_range_label($i)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Add Restaurant</button>
                        <a class="btn btn-secondary" href="list.php">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
