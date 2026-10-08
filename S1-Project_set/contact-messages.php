<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_role('admin');

$pageTitle = 'Contact messages — PhonePulse';
$messages = get_contact_messages();
require __DIR__ . '/header.php';
?>

<h1>Contact messages</h1>
<p class="sub">Messages submitted through the Contact Us form.</p>

<?php if (!$messages): ?>
  <p class="sub">There are no messages yet.</p>
<?php else: ?>
  <div class="contact-messages">
    <?php foreach ($messages as $message): ?>
      <article class="contact-message">
        <div class="contact-message-heading">
          <strong><?= e($message['name']) ?></strong>
          <a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a>
          <time><?= e($message['created_at']) ?></time>
        </div>
        <p><?= nl2br(e($message['message'])) ?></p>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/footer.php'; ?>

