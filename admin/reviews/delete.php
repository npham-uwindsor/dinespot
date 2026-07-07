<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

require_admin();

$id = (int) ($_GET['id'] ?? 0);
$review = get_review_by_id_for_admin($id);

if (!$review) {
    header('Location: list.php?error=Review not found.');
    exit;
}

delete_review_by_id_admin($id);
header('Location: list.php?success=Review has been deleted.');
exit;
