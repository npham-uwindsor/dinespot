<?php

$assetPrefix = '../../';
$activeAdminPage = 'theme';

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

require_admin();

$pageTitle = 'Theme Settings';
$pageDescription = 'Manage the default DineSpot colour theme for all visitors.';
$bodyClass = 'page-client page-admin';

$themes = get_available_themes();
$activeTheme = get_site_theme();
$previewTheme = get_active_theme();
$error = '';
$success = isset($_GET['updated']) ? $_GET['updated'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedTheme = $_POST['active_theme'] ?? '';

    if (!array_key_exists($selectedTheme, $themes)) {
        $error = 'Please choose a valid theme.';
    } elseif (!set_site_theme($selectedTheme)) {
        $error = 'Unable to save the selected theme.';
    } else {
        set_theme_cookie($selectedTheme);
        header('Location: settings.php?updated=Site theme updated successfully.');
        exit;
    }

    $activeTheme = $selectedTheme;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Theme Settings</h1>
        <p class="lead">Choose the default colour theme shown across the public site.</p>
    </div>
</section>

<section class="page-content">
    <div class="container client-layout">
        <?php require admin_path('management/_sidebar.php'); ?>

        <div class="client-main">
            <div class="content-card">
                <?php if ($error !== ''): ?>
                    <div class="alert alert-error" role="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <?php if ($success !== ''): ?>
                    <div class="alert alert-success" role="status"><?= e($success) ?></div>
                <?php endif; ?>

                <h2>Site Theme</h2>
                <p class="form-help">
                    The selected theme becomes the default for all visitors. You can preview a theme before saving,
                    and your browser may remember a personal preview until you save a new site default.
                </p>

                <?php if ($previewTheme !== $activeTheme): ?>
                    <p class="theme-preview-note">
                        You are currently previewing <strong><?= e(theme_label($previewTheme)) ?></strong>.
                        The saved site default is <strong><?= e(theme_label($activeTheme)) ?></strong>.
                    </p>
                <?php else: ?>
                    <p class="theme-preview-note">
                        Active site theme: <strong><?= e(theme_label($activeTheme)) ?></strong>
                    </p>
                <?php endif; ?>

                <form class="theme-settings-form" method="post" action="settings.php">
                    <fieldset class="theme-options">
                        <legend class="sr-only">Choose a site theme</legend>
                        <?php foreach ($themes as $themeId => $theme): ?>
                            <?php $isSelected = $activeTheme === $themeId; ?>
                            <div class="theme-option<?= $isSelected ? ' is-selected' : '' ?>">
                                <label class="theme-option-label">
                                    <input
                                        type="radio"
                                        name="active_theme"
                                        value="<?= e($themeId) ?>"
                                        <?= $isSelected ? 'checked' : '' ?>
                                    >
                                    <span class="theme-option-body">
                                        <span class="theme-option-swatches" aria-hidden="true">
                                            <span style="background-color: <?= e($theme['primary']) ?>"></span>
                                            <span style="background-color: <?= e($theme['accent']) ?>"></span>
                                            <span style="background-color: <?= e($theme['background']) ?>"></span>
                                        </span>
                                        <span class="theme-option-copy">
                                            <span class="theme-option-title"><?= e($theme['label']) ?></span>
                                            <span class="theme-option-description"><?= e($theme['description']) ?></span>
                                        </span>
                                    </span>
                                </label>
                                <a class="btn btn-secondary theme-option-preview" href="<?= e(theme_switch_url($themeId)) ?>">Preview</a>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Site Theme</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
