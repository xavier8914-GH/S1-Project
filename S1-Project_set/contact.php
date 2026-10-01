<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nameInput = $_POST['name'] ?? '';
    $emailInput = $_POST['email'] ?? '';
    $messageInput = $_POST['message'] ?? '';
    $name = is_string($nameInput) ? trim($nameInput) : '';
    $email = is_string($emailInput) ? trim($emailInput) : '';
    $message = is_string($messageInput) ? trim($messageInput) : '';

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try submitting the form again.';
    } elseif ($name === '' || mb_strlen($name) > 100
        || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || mb_strlen($email) > 254
        || $message === '' || mb_strlen($message) > 5000) {
        $error = 'Please enter a valid name, email address, and message.';
    } else {
        add_contact_message($name, $email, $message);
        header('Location: ' . app_url('contact.php') . '?sent=1');
        exit;
    }
}

$pageTitle = 'Contact Us — PhonePulse';
require __DIR__ . '/header.php';
?>

<section class="contact-page">
  <form class="panel contact-panel" method="post">
    <h1>Contact Us</h1>
    <p class="sub">Questions or feedback? Send us a message.</p>

    <?php if ($error !== ''): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <?php if (isset($_GET['sent'])): ?><p class="notice">Thanks for reaching out. Your message has been sent.</p><?php endif; ?>

    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="contact-name">Name</label>
    <input id="contact-name" name="name" maxlength="100" required value="<?= e(is_string($_POST['name'] ?? null) ? $_POST['name'] : '') ?>">

    <label for="contact-email">Email</label>
    <input id="contact-email" name="email" type="email" maxlength="254" required value="<?= e(is_string($_POST['email'] ?? null) ? $_POST['email'] : '') ?>">

    <label for="contact-message">Message</label>
    <textarea id="contact-message" name="message" rows="6" maxlength="5000" required><?= e(is_string($_POST['message'] ?? null) ? $_POST['message'] : '') ?></textarea>

    <button type="submit">Send message</button>
  </form>
</section>

<?php require __DIR__ . '/footer.php'; ?>
