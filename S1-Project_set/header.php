<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'PhonePulse';
$user = current_user();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>

<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <div class="nav-row">
    <a href="<?= e(app_url('index.php')) ?>" class="brand">Phone<span>Pulse</span></a>

    <nav>
      <a href="<?= e(app_url('index.php')) ?>">Home</a>
      <a href="<?= e(app_url('news.php')) ?>">News</a>
      <a href="<?= e(app_url('contact.php')) ?>">Contact Us</a>
      <?php if (has_role('admin', 'salesman')): ?>
        <a href="<?= e(app_url('brands.php')) ?>">Manage brands</a>
        <a href="<?= e(app_url('phones.php')) ?>">Manage phones</a>
        <a href="<?= e(app_url('news-info.php')) ?>">Manage news</a>
      <?php endif; ?>
      <?php if (has_role('admin')): ?>
        <a href="<?= e(app_url('dashboard.php')) ?>">Dashboard</a>
        <a href="<?= e(app_url('users.php')) ?>">Users</a>
        <a href="<?= e(app_url('contact-messages.php')) ?>">Contact messages</a>
      <?php endif; ?>
    </nav>

    <form class="search" action="<?= e(app_url('index.php')) ?>" method="get">
      <input type="text" name="q" placeholder="Search a phone…" value="<?= e($_GET['q'] ?? '') ?>">
    </form>

    <div class="auth">
      <?php if ($user): ?>
        <span class="user-chip"><?= e($user['username']) ?> · <?= e($user['role']) ?></span>
        <form method="post" action="<?= e(app_url('logout.php')) ?>">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <button class="auth-btn" type="submit">Log out</button>
        </form>
      <?php else: ?>
        <a class="auth-btn" href="<?= e(app_url('login.php')) ?>">Log in</a>
        <a class="auth-btn primary" href="<?= e(app_url('signup.php')) ?>">Sign up</a>
      <?php endif; ?>
    </div>

  </div>
</header>
<main>