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
    } elseif (($_POST['agree_terms'] ?? '') !== '1') {
        $error = 'Please read and accept the Terms of Use and Privacy Policy to create an account.';
    } elseif ($username === '' || $password === '') {
        $error = 'Enter a username and password.';
    } 
    elseif (find_user_by_username($username)) {
        $error = 'That username is taken.';
    } 
    else {
        $role = count_users() === 0 ? 'admin' : 'viewer';
        create_user($username, $password, $role);
        log_in_user(find_user_by_username($username));
        header('Location: ' . app_url('index.php'));

        exit;
    }
}

$pageTitle = 'Sign up — PhonePulse';
require __DIR__ . '/header.php';
?>

<form class="panel" method="post">
  <h2>Sign up</h2>

  <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label for="username">Username</label>
  <input id="username" name="username" required>

  <label for="password">Password</label>
  <input id="password" name="password" type="password" required>

  <label class="policy-agreement">
    <input type="checkbox" name="agree_terms" value="1" required <?= ($_POST['agree_terms'] ?? '') === '1' ? 'checked' : '' ?>>
    <span>I have read and agree to the <a href="<?= e(app_url('terms.php')) ?>" target="_blank" rel="noopener">Terms of Use</a> and <a href="<?= e(app_url('privacy.php')) ?>" target="_blank" rel="noopener">Privacy Policy</a>.</span>
  </label>

  <button type="submit">Create account</button>
  <p class="switch-line">Have an account? <a href="<?= e(app_url('login.php')) ?>">Log in</a></p>

</form>
<?php require __DIR__ . '/footer.php'; ?>
