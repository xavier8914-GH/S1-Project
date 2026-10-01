<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_role('admin', 'salesman');

$userId = current_user()['id'];
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $formError = 'Your session expired. Please reload the page and try again.';
  } else {
    if (isset($_POST['delete_id'])) {
      delete_news((int) $_POST['delete_id']);
    }
    else {
      save_news([
        'source'       => trim($_POST['source']),
        'published_on' => $_POST['published_on'] !== '' ? $_POST['published_on'] : null,
        'content'      => trim($_POST['content']),
        'phone_id'     => $_POST['phone_id'] !== '' ? (int) $_POST['phone_id'] : null,
      ], $_POST['id'] ? (int) $_POST['id'] : null, $userId);
    }

    header('Location: ' . app_url('news-info.php'));
    exit;
    }
}

$pageTitle = 'Manage news — PhonePulse';
require __DIR__ . '/header.php';
?>

<h1>Manage news</h1>
<?php if ($formError !== ''): ?><p class="error"><?= e($formError) ?></p><?php endif; ?>
<form class="panel" method="post">

  <h2>Add a news item</h2>
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label for="source">Source</label>
  <input id="source" name="source" required placeholder="e.g. Apple Newsroom">

  <label for="published_on">Published on (blank = "coming soon")</label>
  <input id="published_on" name="published_on" type="date">

  <label for="phone_id">Related phone (optional)</label>
  <select id="phone_id" name="phone_id">
    <option value="">— none —</option>
    <?php foreach (get_phones() as $phone): ?>
      <option value="<?= $phone['id'] ?>"><?= e($phone['name']) ?></option>
    <?php endforeach; ?>
  </select>
  
  <label for="content">Content</label>
  <textarea id="content" name="content" rows="3" required></textarea>
  <button type="submit">Add news</button>

</form>

<table>
  <thead><tr><th>Source</th><th>Date</th><th>Content</th><th></th></tr></thead>
  <tbody>
  <?php foreach (get_news() as $item): ?>

    <tr>
      <td><?= e($item['source']) ?></td>
      <td><?= e($item['published_on'] ?: 'Coming soon') ?></td>
      <td><?= e(mb_strimwidth($item['content'], 0, 60, '…')) ?></td>
      <td class="table-actions">
        <form method="post" style="display:inline">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="delete_id" value="<?= $item['id'] ?>">
          <a href="#" class="danger" onclick="if(confirm('Delete this news item?')){this.closest('form').submit();} return false;">Delete</a>
        </form>
      </td>
    </tr>

  <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/footer.php'; ?>