<?php

// === Session setup ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === App URL and auth helpers ===
function app_url(string $path = ''): string
{
    $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $basePath = rtrim($basePath, '/');
    if ($basePath === '.' || $basePath === '/') {
        $basePath = '';
    }

    return $basePath . '/' . ltrim($path, '/');
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(mixed $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function current_role(): ?string
{
    return current_user()['role'] ?? null;
}

function has_role(string ...$roles): bool
{
    $currentRole = current_role();
    if ($currentRole === null) {
        return false;
    }

    $roleAliases = [$currentRole];
    if ($currentRole === 'editor') {
        $roleAliases[] = 'salesman';
    }
    if ($currentRole === 'salesman') {
        $roleAliases[] = 'editor';
    }
    if ($currentRole === 'saleman') {
        $roleAliases[] = 'salesman';
        $roleAliases[] = 'editor';
    }

    foreach ($roleAliases as $roleAlias) {
        if (in_array($roleAlias, $roles, true)) {
            return true;
        }
    }

    return false;
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . app_url('login.php'));
        exit;
    }
}


function require_role(string ...$roles): void
{
    require_login();
    if (!has_role(...$roles)) {
        http_response_code(403);
        require __DIR__ . '/403.php';
        exit;
    }
}

function log_in_user(array $user): void
{
    session_regenerate_id(true);
    unset($_SESSION['csrf_token']);

    // Only the fields the templates need are kept in the session;
    // the password hash never leaves the users table.
    $_SESSION['user'] = [
        'id'       => $user['id'],
        'username' => $user['username'],
        'role'     => $user['role'],
    ];
}

function log_out_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $cookieParams = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $cookieParams['path'], $cookieParams['domain'], $cookieParams['secure'], $cookieParams['httponly']);
    }

    session_destroy();
}

?>
