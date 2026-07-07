<?php

/** @var string $activeAdminPage */

$activeAdminPage = $activeAdminPage ?? '';

$adminNavItems = [
    ['id' => 'dashboard', 'href' => admin_path('dashboard.php'), 'label' => 'Dashboard'],
    ['id' => 'profile', 'href' => admin_path('management/profile.php'), 'label' => 'My Account'],
    ['id' => 'users', 'href' => admin_path('users/list.php'), 'label' => 'Manage Users'],
    ['id' => 'restaurants', 'href' => admin_path('restaurants/list.php'), 'label' => 'Manage Restaurants'],
    ['id' => 'reservations', 'href' => admin_path('reservations/list.php'), 'label' => 'Manage Reservations'],
    ['id' => 'reviews', 'href' => admin_path('reviews/list.php'), 'label' => 'Manage Reviews'],
    ['id' => 'charts', 'href' => e($assetPrefix ?? '../') . 'charts/index.php', 'label' => 'Insights'],
    ['id' => 'theme', 'href' => admin_path('theme/settings.php'), 'label' => 'Theme Settings'],
    ['id' => 'monitor', 'href' => asset_prefix() . 'monitor.php', 'label' => 'System Monitoring'],
];
?>
<aside class="client-sidebar" aria-label="Admin navigation">
    <div class="client-sidebar-header">
        <p class="client-sidebar-label">DineSpot Admin</p>
    </div>
    <nav class="client-sidebar-nav">
        <ul>
            <?php foreach ($adminNavItems as $item): ?>
                <?php $isActive = $activeAdminPage === $item['id']; ?>
                <li>
                    <a
                        href="<?= e($item['href']) ?>"
                        class="<?= $isActive ? 'is-active' : '' ?>"
                        <?= $isActive ? 'aria-current="page"' : '' ?>
                    ><?= e($item['label']) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>
