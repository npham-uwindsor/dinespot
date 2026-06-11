<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'How to Use DineSpot';
$pageDescription = 'Step-by-step guide for browsing restaurants, booking tables, saving favourites, and writing reviews on DineSpot.';
$bodyClass = 'page-guide';

$steps = [
    [
        'title' => 'Browse and search restaurants',
        'body' => 'Start on the home page or open Restaurants to see featured listings. Use Search to filter by keyword, cuisine, or city.',
        'link' => 'restaurants/search.php',
        'link_label' => 'Try Search',
    ],
    [
        'title' => 'Open a restaurant page',
        'body' => 'Select any restaurant to view photos, menu tabs, map location, and reviews from other diners.',
        'link' => 'restaurants/index.php',
        'link_label' => 'Browse Restaurants',
    ],
    [
        'title' => 'Create your account',
        'body' => 'Register with your name, email, and phone number. An account is required for favourites, reservations, and reviews.',
        'link' => 'client/register.php',
        'link_label' => 'Create Account',
    ],
    [
        'title' => 'Save favourites and reserve a table',
        'body' => 'Sign in, open a restaurant page, tap the heart icon to save it, then choose Reserve a Table to pick date, time, and party size.',
        'link' => 'client/login.php',
        'link_label' => 'Sign In',
    ],
    [
        'title' => 'Manage your account area',
        'body' => 'Use Profile to update your details, My Reservations to track bookings, My Favourites for saved spots, and My Reviews for feedback you have posted.',
        'link' => 'client/profile.php',
        'link_label' => 'Go to Profile',
    ],
    [
        'title' => 'Write and edit reviews',
        'body' => 'After dining, return to the restaurant page and submit a star rating with comments. You can edit your review later from My Reviews.',
        'link' => 'client/my_reviews.php',
        'link_label' => 'View My Reviews',
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>How to Use DineSpot</h1>
        <p class="lead">Follow these steps to discover restaurants, book tables, and manage your account.</p>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <div class="guide-layout">
            <ol class="guide-steps" id="guide-steps">
                <?php foreach ($steps as $index => $step): ?>
                    <li class="guide-step<?= $index === 0 ? ' is-active' : '' ?>" data-guide-step>
                        <button
                            type="button"
                            class="guide-step-button"
                            data-guide-trigger="<?= $index ?>"
                            aria-current="<?= $index === 0 ? 'step' : 'false' ?>"
                        >
                            Step <?= $index + 1 ?>: <?= e($step['title']) ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ol>

            <div class="content-card guide-panel">
                <?php foreach ($steps as $index => $step): ?>
                    <article
                        class="guide-panel-item<?= $index === 0 ? ' is-active' : '' ?>"
                        id="guide-panel-<?= $index ?>"
                        data-guide-panel="<?= $index ?>"
                        <?= $index === 0 ? '' : 'hidden' ?>
                    >
                        <p class="guide-step-label">Step <?= $index + 1 ?> of <?= count($steps) ?></p>
                        <h2><?= e($step['title']) ?></h2>
                        <p><?= e($step['body']) ?></p>
                        <a class="btn btn-primary" href="<?= e($step['link']) ?>"><?= e($step['link_label']) ?></a>
                    </article>
                <?php endforeach; ?>

                <div class="guide-nav">
                    <button type="button" class="btn btn-secondary" id="guide-prev" disabled>Previous</button>
                    <button type="button" class="btn btn-primary" id="guide-next">Next</button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
