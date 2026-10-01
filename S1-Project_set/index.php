<?php
// === Main page setup ===
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

// === Search and data ===
$search = trim($_GET['q'] ?? '');
$phones = get_phones($search);
$favoriteIds = is_logged_in() ? get_favorite_ids(current_user()['id']) : [];
$commentCounts = get_phone_comment_counts(array_column($phones, 'id'));

$pageTitle = 'PhonePulse — Every phone, one clear view';
require __DIR__ . '/header.php';
?>
<?php // === Hero section === ?>
<h1>Every phone, one clear view.</h1>
<p class="sub">Specs and prices for the flagships everyone's asking about, in MYR.</p>
<?php if (isset($_GET['favorite_error'])): ?>
  <p class="error">Your favorite could not be updated. Please try again.</p>
<?php endif; ?>

<?php if ($search !== ''): ?>
  <p class="sub">Showing results for "<?= e($search) ?>" — <a href="<?= e(app_url('index.php')) ?>">clear search</a></p>
<?php endif; ?>
<?php // === Phone cards === ?>
<div class="grid">
<?php foreach ($phones as $phone): ?>
  <div class="card" id="phone-<?= (int) $phone['id'] ?>">
    <?php if (is_logged_in()): ?>

      <form class="fav-form" action="<?= e(app_url('favorite.php')) ?>" method="post">
        <input type="hidden" name="phone_id" value="<?= (int) $phone['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <button class="fav-btn<?= in_array($phone['id'], $favoriteIds) ? ' active' : '' ?>" type="submit">
          <?= in_array($phone['id'], $favoriteIds) ? '♥' : '♡' ?>
        </button>
      </form>

    <?php endif; ?>
    <?php if (!empty($phone['image_path'])): ?>
      <div class="phone-image-frame">
        <img class="phone-image" src="<?= e(app_url($phone['image_path'])) ?>" alt="<?= e($phone['name']) ?>">
      </div>
    <?php else: ?>
      <div class="thumb">📱</div>
    <?php endif; ?>
    <h3><?= e($phone['name']) ?></h3>
    <div class="meta"><?= e($phone['brand_name']) ?></div>
    <p class="highlight"><?= e($phone['spec_highlight']) ?></p>
    <div class="pills">

      <?php if ($phone['status'] === 'unreleased'): ?>
        <span class="pill soon">Unreleased</span><span class="price">TBA</span>
      <?php else: ?>
        <span class="pill">In stock</span><span class="price">RM <?= number_format((float) $phone['price_myr'], 2) ?></span>
      <?php endif; ?>

  </div>

    <div class="card-actions">
      <a class="btn primary" href="<?= e(get_gsmarena_url($phone)) ?>" target="_blank" rel="noopener"><?= has_gsmarena_detail_url($phone) ? 'GSMArena details' : 'Search GSMArena' ?></a>
      <a class="btn" href="<?= e($phone['official_url']) ?>" target="_blank" rel="noopener">Official</a>
    </div>

    <a class="btn view-chat" href="<?= e(app_url('phone-chat.php?phone=' . (int) $phone['id'])) ?>">
      View chat (<?= $commentCounts[$phone['id']] ?? 0 ?>)
    </a>
  </div>

<?php endforeach; ?>
<?php if (!$phones): ?>
  <p class="sub">No phones match that search.</p>
<?php endif; ?>
</div>

<?php require __DIR__ . '/footer.php'; ?>