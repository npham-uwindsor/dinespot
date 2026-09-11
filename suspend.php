<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

require_admin();

$id = $_GET['id'];
$user = get_user_by_id($id);


if (!$user) {
    header('Location: list.php?error=User not found.');
    exit;
}

update_user_status($id, 'suspended');
header('Location: list.php?success=User '. $id .' has been suspended.');
exit;