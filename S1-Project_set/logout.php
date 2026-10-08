<?php

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
	http_response_code(403);
	exit('Invalid logout request.');
}

log_out_user();
header('Location: ' . app_url('index.php'));

exit;
