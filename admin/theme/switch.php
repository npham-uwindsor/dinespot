<?php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

$themes = array_keys(get_available_themes());
$theme = $_GET['theme'] ?? '';

if (in_array($theme, $themes, true)) {
    set_theme_cookie($theme, true);
}

// Relative default (this file is in admin/theme/) — works with or without /dinespot
$redirect = $_GET['redirect'] ?? '../../index.php';
$separator = str_contains($redirect, '?') ? '&' : '?';
header('Location: ' . $redirect . $separator . 'updated=Preview theme applied. It will be available for 5 minutes.');
exit;
