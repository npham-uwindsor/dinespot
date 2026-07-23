<?php

$assetPrefix = '../';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$id = (int) ($_GET['id'] ?? 0);
$restaurant = null;
$menuGrouped = [];
$reviews = [];
$dbError = null;

try {
    $restaurant = get_restaurant_by_id($id);
    if ($restaurant) {
        $menuGrouped = get_menu_items_grouped($id);
        $reviews = get_reviews_for_restaurant($id);
    }
} catch (Throwable $e) {
    $dbError = 'Unable to load restaurant details.';
}

if (!$restaurant && !$dbError) {
    http_response_code(404);
    $pageTitle = 'Restaurant Not Found';
    $pageDescription = 'The requested restaurant could not be found on DineSpot.';
} else {
    $pageTitle = $restaurant['name'];
    $pageDescription = $restaurant['description'];
}

$bodyClass = 'page-restaurant-view';
$mapLat = $restaurant['latitude'] ?? null;
$mapLng = $restaurant['longitude'] ?? null;
$extraHead = '';

if ($restaurant && $mapLat && $mapLng) {
    $extraHead = '
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">';
}

$favouriteIcon = $assetPrefix . 'assets/images/svg/favorite_24dp_E3E3E3_FILL0_wght400_GRAD0_opsz24.svg';
$restaurantViewUrl = $assetPrefix . 'restaurants/view.php?id=' . $id;
$loginForFavAndResUrl = client_path('login.php') . '?redirect=' . urlencode($restaurantViewUrl);
$isFavourited = false;
$isReserved = false;

$userReview = null;
$activeReservation = null;
if ($restaurant && is_client()) {
    $userId = (int) current_user_id();
    $isFavourited = is_restaurant_favourited($userId, $id);
    $isReserved = is_restaurant_reserved($userId, $id);
    $userReview = get_user_review_for_restaurant($userId, $id);
    if ($isReserved) {
        $activeReservation = get_active_reservation_for_restaurant($userId, $id);
    }
}

$favouriteActionUrl = client_path('favourite_toggle.php') . '?restaurant_id=' . $id . '&redirect=' . urlencode($restaurantViewUrl);
$reservationActionUrl = client_path('reservation_toggle.php') . '?restaurant_id=' . $id . '&redirect=' . urlencode($restaurantViewUrl);
$favouriteHref = $loginForFavAndResUrl;
$reservationHref = $loginForFavAndResUrl;

if (is_client()) {
    $favouriteHref = $favouriteActionUrl;
    $reservationHref = $reservationActionUrl;
}

require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($dbError): ?>
    <section class="page-content">
        <div class="container">
            <div class="alert alert-error" role="alert"><?= e($dbError) ?></div>
            <a href="index.php">&larr; Back to restaurants</a>
        </div>
    </section>
<?php elseif (!$restaurant): ?>
    <section class="page-hero">
        <div class="container">
            <h1>Restaurant Not Found</h1>
            <p class="lead">This restaurant does not exist or is no longer available.</p>
            <a class="btn btn-primary" href="index.php">Browse Restaurants</a>
        </div>
    </section>
<?php else: ?>
    <!-- Restaurant hero: image, details, favourite & reserve actions -->
    <section class="restaurant-hero">
        <div class="container">
            <?php if (isset($_GET['cancelled'])): ?>
                <div class="alert alert-success" role="status">Your reservation has been cancelled.</div>
            <?php endif; ?>
        </div>
        <div class="container restaurant-hero-grid">
            <div class="restaurant-hero-image">
                <img src="<?= e(restaurant_image_url($restaurant, $assetPrefix)) ?>" 
                alt="<?= e($restaurant['name']) ?>" 
                <?php if (!empty($restaurant['image_credit']) && str_contains($restaurant['image_credit'], ' on ')): ?>
                    title="<?= e($restaurant['name']) ?> - Photo by <?= e($restaurant['image_credit']) ?>"
                <?php elseif (!empty($restaurant['image_credit']) && !str_contains($restaurant['image_credit'], ' on ')): ?>
                    title="<?= e($restaurant['name']) ?> - Photo source: <?= e($restaurant['image_credit']) ?>"
                <?php else: ?>
                    title="<?= e($restaurant['name']) ?>"
                <?php endif; ?>
                >
                
                <?php if (!empty($restaurant['image_credit']) && str_contains($restaurant['image_credit'], ' on ')): ?>
                    <span class="image-credit">Photo by <?= e($restaurant['image_credit']) ?></span>
                <?php elseif (!empty($restaurant['image_credit']) && !str_contains($restaurant['image_credit'], ' on ')): ?>
                    <span class="image-credit">Photo source: <?= e($restaurant['image_credit']) ?></span>
                <?php endif; ?>
            </div>
            <div class="restaurant-hero-info">
                <p class="restaurant-card-meta">
                    <span><?= e($restaurant['cuisine']) ?> | <?= e($restaurant['city']) ?>, <?= e($restaurant['province']) ?></span>
                </p>
                <h1><?= e($restaurant['name']) ?></h1>
                <p><?= e($restaurant['description']) ?></p>
                <div class="restaurant-hero-details">
                    <p><strong>Price range:</strong> <?= e(price_range_label((int) $restaurant['price_range'])) ?></p>
                    <?php if ((int) $restaurant['review_count'] > 0): ?>
                        <p><strong>Rating:</strong> <?= e(format_rating((float) $restaurant['avg_rating'])) ?> ★ (<?= (int) $restaurant['review_count'] ?> reviews)</p>
                    <?php endif; ?>
                    <?php if (!empty($restaurant['address'])): ?>
                        <p><strong>Address:</strong> <?= e($restaurant['address']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="hero-actions">
                    <?php if (should_use_client_path()): ?>
                    <a
                        class="btn btn-primary btn-favourite<?= $isFavourited ? ' is-favourited' : '' ?>"
                        href="<?= e($favouriteHref) ?>"
                    >
                        <img
                            class="btn-icon-favourite"
                            src="<?= e($favouriteIcon) ?>"
                            alt=""
                            aria-hidden="true"
                        >
                    </a>
                    <?php endif; ?>
                    <?php if (is_client() && $isReserved && $activeReservation): ?>
                        <button
                            type="button"
                            class="btn btn-primary btn-reservation is-reserved"
                            data-cancel-reservation
                            data-reservation-id="<?= (int) $activeReservation['id'] ?>"
                            data-restaurant-name="<?= e($restaurant['name']) ?>"
                            data-reservation-date="<?= e(date('F j, Y', strtotime($activeReservation['reservation_date']))) ?>"
                            data-reservation-time="<?= e(date('g:i A', strtotime($activeReservation['reservation_time']))) ?>"
                            data-party-size="<?= (int) $activeReservation['party_size'] ?>"
                            data-redirect="<?= e($restaurantViewUrl) ?>"
                            data-form-action="<?= e(client_path('cancel_reservation.php')) ?>"
                        >
                            Reserved
                        </button>
                    <?php elseif (should_use_client_path()): ?>
                        <a
                            class="btn btn-primary btn-reservation"
                            href="<?= e($reservationHref) ?>"
                        >
                            Reserve a Table
                        </a>
                    <?php endif; ?>
                    <a class="btn btn-secondary" href="index.php">&larr; All Restaurants</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu tabs, meal calculator, map, and reviews -->
    <section class="page-content">
        <div class="container">
            <?php
            $contextHelpIntro = 'Learn how to read menus, use the meal calculator, and save favourites.';
            require __DIR__ . '/../includes/partials/context-help.php';
            ?>
        </div>
        <div class="container content-grid two-col">
            <!-- Tabbed menu by category -->
            <div class="content-card">
                <h2>Menu</h2>
                <?php if ($menuGrouped === []): ?>
                    <p>Menu information is not available yet.</p>
                <?php else: ?>
                    <div class="menu-tabs" role="tablist" aria-label="Restaurant menu categories">
                        <?php $first = true; foreach (array_keys($menuGrouped) as $category): ?>
                            <button
                                class="menu-tab<?= $first ? ' is-active' : '' ?>"
                                type="button"
                                role="tab"
                                aria-selected="<?= $first ? 'true' : 'false' ?>"
                                aria-controls="menu-panel-<?= e(preg_replace('/[^a-z0-9]+/i', '-', strtolower($category))) ?>"
                                data-menu-tab
                            ><?= e($category) ?></button>
                        <?php $first = false; endforeach; ?>
                    </div>
                    <?php $first = true; foreach ($menuGrouped as $category => $items): ?>
                        <?php $panelId = 'menu-panel-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($category)); ?>
                        <div
                            class="menu-panel<?= $first ? ' is-active' : '' ?>"
                            id="<?= e($panelId) ?>"
                            role="tabpanel"
                            data-menu-panel
                        >
                            <ul class="menu-list">
                                <?php foreach ($items as $item): ?>
                                    <li class="menu-item">
                                        <div class="menu-item-header">
                                            <strong><?= e($item['name']) ?></strong>
                                            <span>$<?= e(number_format((float) $item['price'], 2)) ?></span>
                                        </div>
                                        <?php if (!empty($item['description'])): ?>
                                            <p><?= e($item['description']) ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php $first = false; endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($menuGrouped !== []): ?>
                <!-- Interactive meal total estimator (tax + tip calculated in main.js) -->
                <div class="content-card">
                    <h2>Meal Cost Estimator</h2>
                    <p class="form-help">Select menu items and a tip amount to estimate your meal total.</p>

                    <form class="meal-calculator">
                        <fieldset class="meal-calculator-items">
                            <legend>Choose menu items</legend>
                            <?php foreach ($menuGrouped as $category => $items): ?>
                                <div class="meal-calculator-category">
                                    <h3><?= e($category) ?></h3>
                                    <?php foreach ($items as $item): ?>
                                        <?php $itemId = 'meal-item-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($category . '-' . $item['name'])); ?>
                                        <label class="meal-calculator-option" for="<?= e($itemId) ?>">
                                            <input
                                                type="checkbox"
                                                id="<?= e($itemId) ?>"
                                                data-meal-item
                                                value="<?= e($item['price']) ?>"
                                            >
                                            <span><?= e($item['name']) ?></span>
                                            <strong>$<?= e(number_format((float) $item['price'], 2)) ?></strong>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </fieldset>

                        <div class="form-group">
                            <label for="tip_percent">Tip</label>
                            <select id="tip_percent" data-tip-percent>
                                <option value="0">No tip</option>
                                <option value="10">10%</option>
                                <option value="15" selected>15%</option>
                                <option value="18">18%</option>
                                <option value="20">20%</option>
                            </select>
                        </div>

                        <dl class="quote-summary meal-summary" aria-live="polite">
                            <div>
                                <dt>Subtotal</dt>
                                <dd data-meal-subtotal>$0.00</dd>
                            </div>
                            <div>
                                <dt>Tax (13%)</dt>
                                <dd data-meal-tax>$0.00</dd>
                            </div>
                            <div>
                                <dt>Tip</dt>
                                <dd data-meal-tip>$0.00</dd>
                            </div>
                            <div class="quote-summary-total">
                                <dt>Estimated total</dt>
                                <dd data-meal-total>$0.00</dd>
                            </div>
                        </dl>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Leaflet map (initialised in main.js when coordinates exist) -->
            <div class="content-card">
                <h2>Location</h2>
                <?php if ($mapLat && $mapLng): ?>
                    <div
                        id="restaurant-map"
                        class="restaurant-map"
                        data-lat="<?= e((string) $mapLat) ?>"
                        data-lng="<?= e((string) $mapLng) ?>"
                        data-name="<?= e($restaurant['name']) ?>"
                    ></div>
                <?php else: ?>
                    <p>Map coordinates are not available for this location.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="container">
            <!-- User-submitted reviews -->
            <div class="content-card" style="margin-top: 1.5rem;">
                <div class="profile-header">
                    <h2>Reviews</h2>
                    <?php if (is_client()): ?>
                        <?php if ($userReview): ?>
                            <a class="btn btn-secondary" href="<?= e(client_path('edit_review.php?id=' . (int) $userReview['id'])) ?>">Edit Your Review</a>
                        <?php else: ?>
                            <a class="btn btn-primary" href="<?= e(client_path('add_review.php?restaurant_id=' . $id)) ?>">Write a Review</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php if ($reviews === [] && !is_logged_in()): ?>
                    <p>No reviews yet. <a href="<?= e(client_path('login.php?redirect=' . urlencode($restaurantViewUrl))) ?>">Sign in</a> to be the first to review.</p>
                <?php elseif ($reviews === []): ?>
                    <p>No reviews yet. Be the first to share your experience.</p>
                <?php else: ?>
                    <ul class="review-list">
                        <?php foreach ($reviews as $review): ?>
                            <li class="review-item">
                                <div class="review-item-header">
                                    <strong><?= e($review['full_name']) ?></strong>
                                    <span><?= (int) $review['rating'] ?> ★</span>
                                </div>
                                <p><?= e($review['comment']) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if ($mapLat && $mapLng): ?>
        <!-- Leaflet library (map init runs in main.js) -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <?php endif; ?>

    <?php if (is_client()): ?>
        <?php require __DIR__ . '/../includes/partials/confirm-modal.php'; ?>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
