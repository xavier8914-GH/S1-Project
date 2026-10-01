<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_role('admin');

$stats = get_stats();
$pageTitle = 'Dashboard — PhonePulse';
require __DIR__ . '/header.php';
?>

<h1>Admin dashboard</h1>
<p class="sub">A snapshot of the whole catalogue.</p>
<div class="stat-row">

  <div class="stat"><div class="num"><?= $stats['phones'] ?></div><div class="lbl">Phones listed</div></div>
  <div class="stat"><div class="num"><?= $stats['unreleased'] ?></div><div class="lbl">Unreleased</div></div>
  <div class="stat"><div class="num"><?= $stats['users'] ?></div><div class="lbl">Registered users</div></div>
  <div class="stat"><div class="num"><?= $stats['favorites'] ?></div><div class="lbl">Favourites saved</div></div>
  <div class="stat"><div class="num"><?= $stats['news'] ?></div><div class="lbl">News items</div></div>
  
</div>
<?php require __DIR__ . '/footer.php'; ?>