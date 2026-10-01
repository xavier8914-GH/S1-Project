<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$pageTitle = 'News — PhonePulse';
require __DIR__ . '/header.php';
?>

<h1>News</h1>
<p class="sub">Straight from the brands.</p>
<div class="grid">

<?php foreach (get_news() as $item): ?>
<div class="card">

    <div class="meta">
      <strong><?= e($item['source']) ?></strong> · <?= e($item['published_on'] ?: 'Coming soon') ?>
  </div>

    <p class="highlight"><?= e($item['content']) ?></p>
    <?php if ($item['phone_name']): ?><div class="meta">Re: <?= e($item['phone_name']) ?></div><?php endif; ?>

  </div>
<?php endforeach; ?>

</div>
<?php require __DIR__ . '/footer.php'; ?>