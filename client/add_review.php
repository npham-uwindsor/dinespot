<?php

$assetPrefix = '../';
$activeClientPage = 'my_reviews.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login(client_path('login.php'));

$restaurantId = (int) ($_GET['restaurant_id'] ?? $_POST['restaurant_id'] ?? 0);
$userId = (int) current_user_id();
$restaurant = $restaurantId > 0 ? get_restaurant_by_id($restaurantId) : null;
$error = '';
$rating = 5;
$comment = '';

if (!$restaurant) {
    header('Location: ' . $assetPrefix . 'restaurants/index.php');
    exit;
}

if (user_has_reviewed_restaurant($userId, $restaurantId)) {
    $existing = get_user_review_for_restaurant($userId, $restaurantId);
    header('Location: edit_review.php?id=' . (int) $existing['id']);
    exit;
}

$redirectUrl = $assetPrefix . 'restaurants/view.php?id=' . $restaurantId;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (int) ($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $error = 'Please choose a rating between 1 and 5 stars.';
    } elseif ($comment === '') {
        $error = 'Please write a short review comment.';
    } else {
        $stmt = db()->prepare(
            'INSERT INTO reviews (restaurant_id, user_id, rating, comment)
             VALUES (:restaurant_id, :user_id, :rating, :comment)'
        );
        $stmt->execute([
            'restaurant_id' => $restaurantId,
            'user_id' => $userId,
            'rating' => $rating,
            'comment' => $comment,
        ]);

        header('Location: ' . $redirectUrl . '&reviewed=1');
        exit;
    }
}

$pageTitle = 'Write a Review';
$pageDescription = 'Share your experience at ' . $restaurant['name'] . '.';
$bodyClass = 'page-client';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Write a Review</h1>
        <p class="lead">for <?= e($restaurant['name']) ?></p>
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

                <form class="auth-form" method="post" action="add_review.php" novalidate>
                    <input type="hidden" name="restaurant_id" value="<?= $restaurantId ?>">

                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <select id="rating" name="rating" required>
                            <?php for ($stars = 5; $stars >= 1; $stars--): ?>
                                <option value="<?= $stars ?>"<?= (int) $rating === $stars ? ' selected' : '' ?>>
                                    <?= $stars ?> <?= $stars === 1 ? 'star' : 'stars' ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="comment">Your Review</label>
                        <textarea id="comment" name="comment" rows="5" required><?= e($comment) ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                        <a class="btn btn-secondary" href="<?= e($redirectUrl) ?>">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
