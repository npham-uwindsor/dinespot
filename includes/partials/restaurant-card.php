<?php

/** @var array $restaurant */
/** @var string $assetPrefix */

$viewUrl = ($assetPrefix ?? '') . 'restaurants/view.php?id=' . (int) $restaurant['id'];
?>
<!-- Restaurant card partial — used in listing grids -->
<article class="restaurant-card">
    <div class="restaurant-card-image">
        <a href="<?= e($viewUrl) ?>">
            <img
                src="<?= e(restaurant_image_url($restaurant, $assetPrefix ?? '')) ?>"
                alt="<?= e($restaurant['name']) ?>"
                <?php if (!empty($restaurant['image_credit'])): ?>
                    title="<?= e($restaurant['name']) ?> - Photo: <?= e($restaurant['image_credit']) ?>"
                <?php else: ?>
                    title="<?= e($restaurant['name']) ?>"
                <?php endif; ?>
                loading="lazy"
            >
        </a>
        <!-- If the image credit contains a license URL, we need to parse it and display the author, license (URL to the license), and source -->
        <?php if (!empty($restaurant['image_credit']) && str_contains($restaurant['image_credit'], '<https://')): ?>
            <?php
                $licenseUrl = '';
                if (preg_match('/<([^>]+)>/', $restaurant['image_credit'], $matches)) {
                    $licenseUrl = $matches[1];
                }
                $cleanCredit = preg_split('/<[^>]+>/', $restaurant['image_credit'], 2);
                $parts = array_map('trim', explode(',', trim($cleanCredit[0]), 2));
                $author = $parts[0] ?? '';
                $license = $parts[1] ?? '';
                $source = trim($cleanCredit[1] ?? '');
            ?>
            <span class="image-credit">Photo: <?= e($author) ?>, <a href="<?= e($licenseUrl) ?>" target="_blank" rel="noopener noreferrer"><?= e($license) ?></a><?= e($source) ?></span>
        <?php elseif (!empty($restaurant['image_credit'])): ?>
            <span class="image-credit">Photo: <?= e($restaurant['image_credit']) ?></span>
        <?php endif; ?>
    </div>
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
