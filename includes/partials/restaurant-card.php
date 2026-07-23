<?php

/** @var array $restaurant */
/** @var string $assetPrefix */

$viewUrl = ($assetPrefix ?? '') . 'restaurants/view.php?id=' . (int) $restaurant['id'];
?>
<!-- Restaurant card partial — used in listing grids -->
<article class="restaurant-card">
    <a class="restaurant-card-image" href="<?= e($viewUrl) ?>">
        <img
            src="<?= e(restaurant_image_url($restaurant, $assetPrefix ?? '')) ?>"
            alt="<?= e($restaurant['name']) ?>"
            <?php if (!empty($restaurant['image_credit']) && str_contains($restaurant['image_credit'], ' on ')): ?>
                title="<?= e($restaurant['name']) ?> - Photo by <?= e($restaurant['image_credit']) ?>"
            <?php elseif (!empty($restaurant['image_credit']) && !str_contains($restaurant['image_credit'], ' on ')): ?>
                title="<?= e($restaurant['name']) ?> - Photo source: <?= e($restaurant['image_credit']) ?>"
            <?php else: ?>
                title="<?= e($restaurant['name']) ?>"
            <?php endif; ?>
            loading="lazy"
        >
        <?php if (!empty($restaurant['image_credit']) && str_contains($restaurant['image_credit'], ' on ')): ?>
            <span class="image-credit">Photo by <?= e($restaurant['image_credit']) ?></span>
        <?php elseif (!empty($restaurant['image_credit']) && !str_contains($restaurant['image_credit'], ' on ')): ?>
            <span class="image-credit">Photo source: <?= e($restaurant['image_credit']) ?></span>
        <?php endif; ?>
    </a>
    <div class="restaurant-card-body">
        <p class="restaurant-card-meta">
            <span><?= e($restaurant['cuisine']) ?> | <?= e($restaurant['city']) ?>, <?= e($restaurant['province']) ?></span>
        </p>
        <h2><a href="<?= e($viewUrl) ?>"><?= e($restaurant['name']) ?></a></h2>
        <p class="restaurant-card-desc"><?= e($restaurant['description']) ?></p>
        <div class="restaurant-card-footer">
            <span class="price-range"><?= e(price_range_label((int) $restaurant['price_range'])) ?></span>
            <?php if ((int) $restaurant['review_count'] > 0): ?>
                <span class="rating-badge"><?= e(format_rating((float) $restaurant['avg_rating'])) ?> ★ (<?= (int) $restaurant['review_count'] ?>)</span>
            <?php else: ?>
                <span class="rating-badge rating-badge-muted">No reviews yet</span>
            <?php endif; ?>
        </div>
    </div>
</article>
