<?php

$assetPrefix = '../';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Search Restaurants';
$pageDescription = 'Search DineSpot restaurants by keyword, cuisine, and city.';
$bodyClass = 'page-search';

$query = trim($_GET['q'] ?? '');
$cuisine = trim($_GET['cuisine'] ?? '');
$city = trim($_GET['city'] ?? '');

$restaurants = [];
$filterOptions = ['cuisines' => [], 'cities' => []];
$dbError = null;

try {
    $filterOptions = get_filter_options();
    $restaurants = search_restaurants($query, $cuisine, $city);
} catch (Throwable $e) {
    $dbError = 'Unable to search restaurants. Please check your database connection.';
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Search Restaurants</h1>
        <p class="lead">Find restaurants by name, cuisine, city, or description.</p>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <?php
        $contextHelpIntro = 'Learn how to search by keyword, cuisine, and city.';
        require __DIR__ . '/../includes/partials/context-help.php';
        ?>
        <form class="search-form content-card" method="get" action="search.php">
            <div class="search-form-grid">
                <div class="form-group">
                    <label for="q">Keywords</label>
                    <input type="search" id="q" name="q" value="<?= e($query) ?>" placeholder="e.g. sushi, Thai, BBQ">
                </div>
                <div class="form-group">
                    <label for="cuisine">Cuisine</label>
                    <select id="cuisine" name="cuisine">
                        <option value="">All cuisines</option>
                        <?php foreach ($filterOptions['cuisines'] as $option): ?>
                            <option value="<?= e($option) ?>"<?= $cuisine === $option ? ' selected' : '' ?>><?= e($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <select id="city" name="city">
                        <option value="">All cities</option>
                        <?php foreach ($filterOptions['cities'] as $option): ?>
                            <option value="<?= e($option) ?>"<?= $city === $option ? ' selected' : '' ?>><?= e($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php if ($dbError): ?>
            <div class="alert alert-error" role="alert"><?= e($dbError) ?></div>
        <?php else: ?>
            <div class="section-heading" style="margin-top: 1.5rem;">
                <h2><?= count($restaurants) ?> result<?= count($restaurants) === 1 ? '' : 's' ?></h2>
                <?php if ($query !== '' || $cuisine !== '' || $city !== ''): ?>
                    <a href="search.php">Clear filters</a>
                <?php endif; ?>
            </div>

            <?php if ($restaurants === []): ?>
                <div class="content-card">
                    <p>No restaurants match your search. Try different keywords or filters.</p>
                </div>
            <?php else: ?>
                <div class="restaurant-grid">
                    <?php foreach ($restaurants as $restaurant): ?>
                        <?php include __DIR__ . '/../includes/partials/restaurant-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
