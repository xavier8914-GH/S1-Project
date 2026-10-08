<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

if (is_logged_in()) {
    header('Location: ' . app_url('index.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameInput = $_POST['username'] ?? '';
    $passwordInput = $_POST['password'] ?? '';
    $username = is_string($usernameInput) ? trim($usernameInput) : '';
    $password = is_string($passwordInput) ? $passwordInput : '';

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please reload the page and try again.';
    } else {
        $user = find_user_by_username($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            log_in_user($user);
            header('Location: ' . app_url('index.php'));
            exit;
        }

        $error = 'Wrong username or password.';
    }
}

$pageTitle = 'Log in — PhonePulse';
require __DIR__ . '/header.php';
?>

<form class="panel" method="post">
  <h2>Log in</h2>

  <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label for="username">Username</label>
  <input id="username" name="username" required>

  <label for="password">Password</label>
  <input id="password" name="password" type="password" required>
  
  <button type="submit">Log in</button>
  <p class="switch-line">No account? <a href="<?= e(app_url('signup.php')) ?>">Sign up</a></p>

</form>
<?php require __DIR__ . '/footer.php'; ?>
