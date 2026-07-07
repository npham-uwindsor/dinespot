<?php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

$themes = array_keys(get_available_themes());
$theme = $_GET['theme'] ?? '';

if (in_array($theme, $themes, true)) {
    set_theme_cookie($theme);
}

$redirect = $_GET['redirect'] ?? '/';
header('Location: ' . $redirect);
exit;
