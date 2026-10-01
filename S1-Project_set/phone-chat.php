<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$phoneId = filter_input(INPUT_GET, 'phone', FILTER_VALIDATE_INT);
$phone = is_int($phoneId) && $phoneId > 0 ? get_phone($phoneId) : null;

if (!$phone) {
    http_response_code(404);
    $pageTitle = 'Phone not found — PhonePulse';
    require __DIR__ . '/header.php';
    ?>
    <h1>Phone not found</h1>
    <p class="sub">That phone may have been removed.</p>
    <a class="btn" href="<?= e(app_url('index.php')) ?>">Back to phones</a>
    <?php
    require __DIR__ . '/footer.php';
    exit;
}

$commentsByPhone = get_phone_comments([(int) $phone['id']]);
$comments = $commentsByPhone[$phone['id']] ?? [];
$pageTitle = 'Chat — ' . $phone['name'] . ' — PhonePulse';
require __DIR__ . '/header.php';
?>

<section class="phone-chat-page">
  <a class="chat-back" href="<?= e(app_url('index.php')) ?>#phone-<?= (int) $phone['id'] ?>">&larr; Back to phones</a>
  <h1><?= e($phone['name']) ?> chat</h1>
  <p class="sub"><?= count($comments) ?> message<?= count($comments) === 1 ? '' : 's' ?> from PhonePulse users.</p>

  <?php if (isset($_GET['comment_error'])): ?>
    <p class="error">Please enter a comment, then try again.</p>
  <?php elseif (isset($_GET['comment_sent'])): ?>
    <p class="notice">Your comment was added.</p>
  <?php endif; ?>

  <?php if (is_logged_in()): ?>
    <form class="panel chat-comment-form" action="<?= e(app_url('comment.php')) ?>" method="post">
      <input type="hidden" name="phone_id" value="<?= (int) $phone['id'] ?>">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <label for="chat-comment">Comment as <?= e(current_user()['username']) ?></label>
      <textarea id="chat-comment" name="body" maxlength="2000" rows="3" required></textarea>
      <button type="submit">Post comment</button>
    </form>
  <?php else: ?>
    <div class="chat-login-prompt">
      <span>Log in to join the conversation.</span>
      <a class="btn primary" href="<?= e(app_url('login.php')) ?>">Log in</a>
    </div>
  <?php endif; ?>

  <div class="chat-messages">
    <?php foreach ($comments as $comment): ?>
      <article class="chat-message">
        <div class="chat-message-heading">
          <strong><?= e($comment['author_name']) ?></strong>
          <time><?= e($comment['created_at']) ?></time>
        </div>
        <p><?= nl2br(e($comment['body'])) ?></p>
      </article>
    <?php endforeach; ?>
    <?php if (!$comments): ?>
      <p class="chat-empty">No messages yet. Start the conversation.</p>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>
