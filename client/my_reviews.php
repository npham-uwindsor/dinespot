<?php

$assetPrefix = '../';
$activeClientPage = 'my_reviews.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login(client_path('login.php'));

$pageTitle = 'My Reviews';
$pageDescription = 'Reviews you have posted on DineSpot.';
$bodyClass = 'page-client';

$userId = current_user_id();
$stmt = db()->prepare(
    'SELECT rv.id, rv.rating, rv.comment, rv.created_at, rest.id AS restaurant_id, rest.name AS restaurant_name
     FROM reviews rv
     INNER JOIN restaurants rest ON rest.id = rv.restaurant_id
     WHERE rv.user_id = :user_id
     ORDER BY rv.created_at DESC'
);
$stmt->execute(['user_id' => $userId]);
$reviews = $stmt->fetchAll();
$success = '';
if (isset($_GET['updated'])) {
    $success = 'Your review has been updated.';
} elseif (isset($_GET['deleted'])) {
    $success = 'Your review has been deleted.';
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>My Reviews</h1>
        <p class="lead">Feedback you have shared with the DineSpot community.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require __DIR__ . '/_sidebar.php'; ?>

        <div class="client-main">
            <?php if ($success !== ''): ?>
                <div class="alert alert-success" role="status"><?= e($success) ?></div>
            <?php endif; ?>
            <?php if ($reviews === []): ?>
                <div class="content-card">
                    <p>You have not written any reviews yet. <a href="<?= e($assetPrefix) ?>restaurants/index.php">Visit a restaurant page</a> to leave your first review.</p>
                </div>
            <?php else: ?>
                <ul class="review-list">
                    <?php foreach ($reviews as $review): ?>
                        <li class="review-item">
                            <div class="review-item-header">
                                <h2 class="review-item-title"><a href="<?= e($assetPrefix) ?>restaurants/view.php?id=<?= (int) $review['restaurant_id'] ?>">
                                    <?= e($review['restaurant_name']) ?>
                                </a></h2>
                                <span><?= (int) $review['rating'] ?> ★</span>
                            </div>
                            <p><?= e($review['comment']) ?></p>
                            <p class="review-date"><?= e(date('F j, Y', strtotime($review['created_at']))) ?></p>
                            <div class="form-actions">
                                <a class="btn btn-secondary" href="edit_review.php?id=<?= (int) $review['id'] ?>">Edit</a>
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-delete-review
                                    data-review-id="<?= (int) $review['id'] ?>"
                                    data-restaurant-name="<?= e($review['restaurant_name']) ?>"
                                    data-redirect="my_reviews.php"
                                    data-form-action="delete_review.php"
                                >
                                    Delete
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/partials/confirm-modal.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
