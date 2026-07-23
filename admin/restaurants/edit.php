<?php

$assetPrefix = '../../';
$activeAdminPage = 'restaurants';

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Edit Restaurant';
$pageDescription = 'Edit DineSpot restaurant listing.';
$bodyClass = 'page-client page-admin';

$error = '';
$success = $_GET['success'] ?? '';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: list.php?error=Restaurant ID is required');
    exit;
}

$restaurant = get_restaurant_by_id_for_admin($id);
if ($restaurant === null) {
    header('Location: list.php?error=Restaurant not found');
    exit;
}

$name = $restaurant['name'];
$cuisine = $restaurant['cuisine'];
$city = $restaurant['city'];
$province = $restaurant['province'];
$description = $restaurant['description'];
$address = $restaurant['address'] ?? '';
$image_path = $restaurant['image_path'] ?? '';
$price_range = (int) $restaurant['price_range'];
$is_active = (int) $restaurant['is_active'];
$image_credit = $restaurant['image_credit'] ?? '';
$menuGrouped = get_menu_items_grouped($id);
$menuItems = [];
foreach ($menuGrouped as $items) {
    foreach ($items as $item) {
        $menuItems[] = $item;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $cuisine = trim($_POST['cuisine'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $province = strtoupper(trim($_POST['province'] ?? ''));
    $description = trim($_POST['description'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $price_range = (int) ($_POST['price_range'] ?? 2);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $image_path = $restaurant['image_path'] ?? '';
    $image_credit = trim($_POST['image_credit'] ?? '');

    if ($name === '' || $cuisine === '' || $city === '' || $province === '' || $description === '' || $address === '') {
        $error = 'Name, cuisine, city, province, description, and address are required.';
    } elseif ($price_range < 1 || $price_range > 4) {
        $error = 'Please select a valid price range.';
    } else {
        $hasNewImage = isset($_FILES['image']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        if ($hasNewImage) {
            $image_result = image_upload('image', 'restaurants', $image_path);
            if (is_array($image_result) && isset($image_result['error'])) {
                $error = $image_result['error'];
            } else {
                $image_path = $image_result['path'] ?? $image_path;
            }
        }

        if ($error === '') {
            $updated = update_restaurant($id, [
                'name' => $name,
                'cuisine' => $cuisine,
                'city' => $city,
                'province' => $province,
                'description' => $description,
                'address' => $address,
                'image_path' => $image_path,
                'price_range' => $price_range,
                'is_active' => $is_active,
                'image_credit' => $image_credit,
            ]);

            if ($updated) {
                header('Location: edit.php?id=' . $id . '&success=Restaurant has been updated.');
                exit;
            }

            $error = 'Failed to update restaurant. Please try again.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Edit Restaurant</h1>
        <p class="lead">Update listing for <?= e($name) ?>.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require admin_path('management/_sidebar.php'); ?>

        <div class="client-main">
            <div class="content-card">
                <?php if ($success !== ''): ?>
                    <div class="alert alert-success" role="status"><?= e($success) ?></div>
                <?php endif; ?>
                <?php if ($error !== ''): ?>
                    <div class="alert alert-error" role="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <?php
                $contextHelpIntro = 'Learn how to update restaurant details, menu items, and listing images.';
                require __DIR__ . '/../../includes/partials/context-help.php';
                ?>

                <h2>Restaurant Details</h2>
                <form class="auth-form" method="post" action="edit.php?id=<?= (int) $id ?>" enctype="multipart/form-data" novalidate>
                    <div class="form-group">
                        <img src="<?= e(restaurant_image_url($restaurant, $assetPrefix)) ?>" alt="<?= e($name) ?>" style="max-width: 300px; height: auto;">
                        <p class="admin-edit-restaurant-image-name"><?= e(basename($restaurant['image_path'] ?? 'File not found')) ?></p>
                        <label for="image">Upload new image</label>
                        <input type="file" name="image" id="image" accept="image/jpeg, image/png, image/jpg">
                        <label for="image_credit">Image Credit</label>
                        <input type="text" id="image_credit" name="image_credit" value="<?= e($image_credit) ?>">
                    </div>
                    <div class="form-group">
                        <label for="name">Name*</label>
                        <input type="text" id="name" name="name" value="<?= e($name) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="cuisine">Cuisine*</label>
                        <input type="text" id="cuisine" name="cuisine" value="<?= e($cuisine) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address*</label>
                        <input type="text" id="address" name="address" value="<?= e($address) ?>">
                    </div>
                    <div class="form-group">
                        <label for="city">City*</label>
                        <input type="text" id="city" name="city" value="<?= e($city) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="province">Province*</label>
                        <input type="text" id="province" name="province" value="<?= e($province) ?>" maxlength="10" placeholder="ON" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description*</label>
                        <textarea id="description" name="description" rows="4" required><?= e($description) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price_range">Price Range*</label>
                        <select id="price_range" name="price_range" required>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <option value="<?= $i ?>"<?= $price_range === $i ? ' selected' : '' ?>><?= e(price_range_label($i)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_active">Active listing</label>
                        <input type="checkbox" id="is_active" name="is_active" value="1"<?= $is_active ? ' checked' : '' ?>>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a class="btn btn-secondary" href="list.php">Cancel</a>
                    </div>
                </form>
            </div>

            <div class="content-card admin-users-card">
                <div class="admin-table-toolbar">
                    <div>
                        <h2>Menu Items</h2>
                        <p class="admin-table-meta"><?= count($menuItems) ?> item<?= count($menuItems) === 1 ? '' : 's' ?></p>
                    </div>
                </div>

                <?php if ($menuItems === []): ?>
                    <p class="admin-table-empty">No menu items found.</p>
                <?php else: ?>
                    <div class="admin-table-scroll" tabindex="0" aria-label="Menu items table">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th scope="col">Category</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menuItems as $item): ?>
                                    <tr>
                                        <td><?= e($item['category']) ?></td>
                                        <td><strong><?= e($item['name']) ?></strong></td>
                                        <td><?= e($item['description'] ?? '') ?></td>
                                        <td>$<?= e(number_format((float) $item['price'], 2)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
