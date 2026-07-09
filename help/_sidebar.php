<?php

/** @var string $activeHelpPage */
require_once __DIR__ . '/../includes/functions.php';

$activeHelpPage = $activeHelpPage ?? basename(current_script_path() ?? '');
$helpNavItems = get_help_pages();
?>
<aside class="client-sidebar help-sidebar" aria-label="Help wiki navigation">
    <div class="client-sidebar-header">
        <p class="client-sidebar-label">Help Wiki</p>
    </div>
    <nav class="client-sidebar-nav">
        <ul>
            <?php foreach ($helpNavItems as $file => $label): ?>
                <?php if ($file === 'updating-content.php' && !is_admin()): continue; ?>
                <?php endif; ?>
                <li>
                    <a
                        href="<?= e($file) ?>"
                        class="<?= $activeHelpPage === $file ? 'is-active' : '' ?>"
                        <?= $activeHelpPage === $file ? 'aria-current="page"' : '' ?>
                    ><?= e($label) ?></a>
                </li>
            <?php endforeach; ?>
            <li>
                <a href="<?= e(asset_prefix()) ?>guide.php">Interactive Guide</a>
            </li>
        </ul>
    </nav>
</aside>
