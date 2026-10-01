<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_role('admin', 'salesman');

$error = '';
$name = '';
$officialUrl = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nameInput = $_POST['name'] ?? '';
    $urlInput = $_POST['official_url'] ?? '';
    $name = is_string($nameInput) ? trim($nameInput) : '';
    $officialUrl = is_string($urlInput) ? trim($urlInput) : '';

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please reload the page and try again.';
    } elseif ($name === '' || mb_strlen($name) > 50) {
        $error = 'Enter a brand name of no more than 50 characters.';
    } elseif ($officialUrl !== '' && (
        mb_strlen($officialUrl) > 255
        || !filter_var($officialUrl, FILTER_VALIDATE_URL)
        || !in_array(strtolower((string) parse_url($officialUrl, PHP_URL_SCHEME)), ['http', 'https'], true)
    )) {
        $error = 'Enter a valid http or https website URL, or leave it blank.';
    } else {
        $duplicate = get_db()->prepare('SELECT id FROM brands WHERE LOWER(name) = LOWER(:name)');
        $duplicate->execute(['name' => $name]);

        if ($duplicate->fetch()) {
            $error = 'That brand already exists.';
        } else {
            add_brand($name, $officialUrl !== '' ? $officialUrl : null);
            header('Location: ' . app_url('brands.php') . '?added=1');
            exit;
        }
    }
}

$pageTitle = 'Manage brands — PhonePulse';
$brands = get_brands();
require __DIR__ . '/header.php';
?>

<h1>Manage brands</h1>
<p class="sub">Add brands here. They will be available in the brand dropdown when you manage phones.</p>

<form class="panel" method="post">
  <h2>Add a brand</h2>
  <?php if ($error !== ''): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
  <?php if (isset($_GET['added'])): ?><p class="notice">Brand added. It is now available when adding or editing a phone.</p><?php endif; ?>

  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label for="brand-name">Brand name</label>
  <input id="brand-name" name="name" maxlength="50" required value="<?= e($name) ?>">

  <label for="official-url">Official website URL</label>
  <input id="official-url" name="official_url" type="url" maxlength="255" placeholder="https://example.com" value="<?= e($officialUrl) ?>">

  <button type="submit">Add brand</button>
</form>

<h2>Current brands</h2>
<table>
  <thead><tr><th>Brand</th><th>Official website</th></tr></thead>
  <tbody>
  <?php foreach ($brands as $brand): ?>
    <tr>
      <td><?= e($brand['name']) ?></td>
      <td>
        <?php if (!empty($brand['official_url'])): ?>
          <a href="<?= e($brand['official_url']) ?>" target="_blank" rel="noopener"><?= e($brand['official_url']) ?></a>
        <?php else: ?>
          <span class="sub">Not provided</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php require __DIR__ . '/footer.php'; ?>
