<?php

/** @var string $activeClientPage */

$activeClientPage = $activeClientPage ?? basename($_SERVER['PHP_SELF'] ?? '');

$clientNavItems = [
    'profile.php' => 'My Account',
    'reservations.php' => 'My Reservations',
    'favourites.php' => 'My Favourites',
    'my_reviews.php' => 'My Reviews',
];
?>
<aside class="client-sidebar" aria-label="Account navigation">
    <div class="client-sidebar-header">
        <p class="client-sidebar-label">DineSpot Client</p>
    </div>
    <nav class="client-sidebar-nav">
        <ul>
            <?php foreach ($clientNavItems as $file => $label): ?>
                <li>
                    <a
                        href="<?= e($file) ?>"
                        class="<?= $activeClientPage === $file ? 'is-active' : '' ?>"
                        <?= $activeClientPage === $file ? 'aria-current="page"' : '' ?>
                    ><?= e($label) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>
