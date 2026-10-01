<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

if (is_logged_in()) {
    header('Location: ' . app_url('index.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = find_user_by_username($username);

    if ($user && password_verify($password, $user['password_hash'])) {
        log_in_user($user);
        header('Location: ' . app_url('index.php'));
        exit;
    }

    $error = 'Wrong username or password.';
}

$pageTitle = 'Log in — PhonePulse';
require __DIR__ . '/header.php';
?>

<form class="panel" method="post">
  <h2>Log in</h2>

  <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
  <label for="username">Username</label>
  <input id="username" name="username" required>

  <label for="password">Password</label>
  <input id="password" name="password" type="password" required>
  
  <button type="submit">Log in</button>
  <p class="switch-line">No account? <a href="<?= e(app_url('signup.php')) ?>">Sign up</a></p>

</form>
<?php require __DIR__ . '/footer.php'; ?>