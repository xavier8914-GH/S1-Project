<?php

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_login();

$phoneId = filter_input(INPUT_POST, 'phone_id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && is_int($phoneId)
    && $phoneId > 0
    && verify_csrf_token($_POST['csrf_token'] ?? null)) {
    toggle_favorite(current_user()['id'], $phoneId);
} else {
    header('Location: ' . app_url('index.php') . '?favorite_error=1');
    exit;
}

header('Location: ' . app_url('index.php'));
exit;

?>