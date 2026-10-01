<?php

require_once __DIR__ . '/auth.php';
log_out_user();
header('Location: ' . app_url('index.php'));

exit;