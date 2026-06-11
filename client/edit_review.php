<?php

$assetPrefix = '../';
$activeClientPage = 'my_reviews.php';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

require_login($assetPrefix . 'client/login.php');

$reviewId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$userId = (int) current_user_id();
$review = $reviewId > 0 ? get_review_by_id_for_user($reviewId, $userId) : null;
$error = '';

if (!$review) {
    header('Location: my_reviews.php');
    exit;
}

$rating = (int) $review['rating'];
$comment = $review['comment'];
$redirectUrl = $assetPrefix . 'restaurants/view.php?id=' . (int) $review['restaurant_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (int) ($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $error = 'Please choose a rating between 1 and 5 stars.';
    } elseif ($comment === '') {
        $error = 'Please write a short review comment.';
    } else {
        $stmt = db()->prepare(
            'UPDATE reviews SET rating = :rating, comment = :comment WHERE id = :id AND user_id = :user_id'
        );
        $stmt->execute([
            'rating' => $rating,
            'comment' => $comment,
            'id' => $reviewId,
            'user_id' => $userId,
        ]);

        header('Location: my_reviews.php?updated=1');
        exit;
    }
}

$pageTitle = 'Edit Review';
$pageDescription = 'Update your review for ' . $review['restaurant_name'] . '.';
$bodyClass = 'page-client';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Edit Review</h1>
        <p class="lead">for <?= e($review['restaurant_name']) ?></p>
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

                <form class="auth-form" method="post" action="edit_review.php?id=<?= $reviewId ?>" novalidate>
                    <input type="hidden" name="id" value="<?= $reviewId ?>">

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
                        <button type="submit" class="btn btn-primary">Save Review</button>
                        <a class="btn btn-secondary" href="my_reviews.php">Cancel</a>
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-delete-review
                            data-review-id="<?= $reviewId ?>"
                            data-restaurant-name="<?= e($review['restaurant_name']) ?>"
                            data-redirect="my_reviews.php"
                            data-form-action="delete_review.php"
                        >
                            Delete Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/partials/confirm-modal.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
