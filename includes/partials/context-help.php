<?php

require_once __DIR__ . '/../functions.php';

$helpPage = $contextHelpPage ?? get_context_help_page();
$helpPages = get_help_pages();
$helpLabel = $helpPages[$helpPage] ?? 'Help';
$helpIntro = $contextHelpIntro ?? 'Open the help wiki page for step-by-step instructions about this feature.';
?>
<aside class="context-help" aria-label="Context-sensitive help">
    <a class="context-help-link" href="<?= e(help_path($helpPage)) ?>">Help: <?= e($helpLabel) ?></a>
    <p><?= e($helpIntro) ?></p>
</aside>
