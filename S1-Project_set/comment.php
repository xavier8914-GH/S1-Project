<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_login();

$phoneId = filter_input(INPUT_POST, 'phone_id', FILTER_VALIDATE_INT);
$bodyInput = $_POST['body'] ?? '';
$body = is_string($bodyInput) ? trim($bodyInput) : '';
$authorName = current_user()['username'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !$phoneId
    || !verify_csrf_token($_POST['csrf_token'] ?? null)
    || $body === ''
    || mb_strlen($body) > 2000
    || get_phone((int) $phoneId) === null) {
    $query = http_build_query(['phone' => max(0, (int) $phoneId), 'comment_error' => 1]);
    header('Location: ' . app_url('phone-chat.php') . '?' . $query);
    exit;
}

add_phone_comment((int) $phoneId, $authorName, $body);
$query = http_build_query(['phone' => (int) $phoneId, 'comment_sent' => 1]);
header('Location: ' . app_url('phone-chat.php') . '?' . $query);
exit;

