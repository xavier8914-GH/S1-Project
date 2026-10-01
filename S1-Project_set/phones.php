<?php
// Admins and salemen can add, edit, and remove phones. One form
// handles both add and edit (DRY): editing pre-fills it via ?edit=id.
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_role('admin', 'salesman');

$userId = current_user()['id'];
$formError = '';

function save_phone_image(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The image upload failed. Please try again with a smaller image.');
    }
    $temporaryFile = $file['tmp_name'] ?? null;
    if (!is_string($temporaryFile)
        || ($file['size'] ?? 0) > 5 * 1024 * 1024
        || !is_uploaded_file($temporaryFile)) {
        throw new RuntimeException('Choose an image smaller than 5 MB.');
    }

    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($fileInfo === false) {
        throw new RuntimeException('Image uploads are unavailable on this server.');
    }
    $mime = finfo_file($fileInfo, $temporaryFile);
    finfo_close($fileInfo);
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    if (!isset($extensions[$mime])) {
        throw new RuntimeException('Use a JPG, PNG, or WebP image.');
    }

    $directory = __DIR__ . '/uploads/phones';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('The phone image folder could not be created.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($temporaryFile, $directory . '/' . $filename)) {
        throw new RuntimeException('The image could not be saved.');
    }

    return 'uploads/phones/' . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $formError = 'Your session expired. Please reload the page and try again.';
    } elseif (isset($_POST['delete_id'])) {
        delete_phone((int) $_POST['delete_id']);
        header('Location: ' . app_url('phones.php'));
        exit;
    } else {
        try {
            $phoneId = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
            $existingPhone = $phoneId ? get_phone($phoneId) : null;
            $uploadedImage = save_phone_image($_FILES['image'] ?? []);

            save_phone([
                'name'   => trim($_POST['name']),
                'brand_id' => (int) $_POST['brand_id'],
                'spec_highlight' => trim($_POST['spec']),
                'price_myr'      => $_POST['price'] !== '' ? $_POST['price'] : null,
                'status' => $_POST['status'],
                'detail_url' => trim($_POST['detail_url']),
                'image_path' => $uploadedImage ?? ($existingPhone['image_path'] ?? null),
            ], $phoneId, $userId);

            header('Location: ' . app_url('phones.php'));
            exit;
        } catch (RuntimeException $error) {
            $formError = $error->getMessage();
        }
    }
}

$editing = null;
if (isset($_GET['edit'])) {
    $editing = get_phone((int) $_GET['edit']);
}

$pageTitle = 'Manage phones — PhonePulse';
require __DIR__ . '/header.php';
?>

<h1>Manage phones</h1>
<form class="panel" method="post" enctype="multipart/form-data">

  <h2><?= $editing ? 'Edit phone' : 'Add a phone' ?></h2>
  <?php if ($formError !== ''): ?><p class="error"><?= e($formError) ?></p><?php endif; ?>
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <input type="hidden" name="id" value="<?= $editing['id'] ?? '' ?>">
  <label for="name">Name</label>
  <input id="name" name="name" required value="<?= e($editing['name'] ?? '') ?>">
  <label for="brand_id">Brand</label>

  <select id="brand_id" name="brand_id" required>
    <?php foreach (get_brands() as $brand): ?>
      <option value="<?= $brand['id'] ?>" <?= ($editing['brand_id'] ?? null) == $brand['id'] ? 'selected' : '' ?>>
        <?= e($brand['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <label for="spec">Spec highlight</label>
  <input id="spec" name="spec" value="<?= e($editing['spec_highlight'] ?? '') ?>">
  <label for="price">Price (MYR, blank if unreleased)</label>
  <input id="price" name="price" type="number" step="0.01" value="<?= e((string)($editing['price_myr'] ?? '')) ?>">
  <label for="status">Status</label>

  <select id="status" name="status">
    <option value="in_stock" <?= ($editing['status'] ?? '') === 'in_stock' ? 'selected' : '' ?>>In stock</option>
    <option value="unreleased" <?= ($editing['status'] ?? '') === 'unreleased' ? 'selected' : '' ?>>Unreleased</option>
  </select>

  <label for="detail_url">GSMArena detail URL (leave blank to search by model)</label>
  <input id="detail_url" name="detail_url" value="<?= e($editing['detail_url'] ?? '') ?>">
  <label for="image">Phone card image (JPG, PNG, or WebP; max 5 MB)</label>
  <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">
  <?php if (!empty($editing['image_path'])): ?>
    <img class="phone-image-preview" src="<?= e(app_url($editing['image_path'])) ?>" alt="<?= e($editing['name']) ?>">
    <p class="sub">Choose a new image to replace this one.</p>
  <?php endif; ?>
  <button type="submit"><?= $editing ? 'Save changes' : 'Add phone' ?></button>

</form>

<table>
  <thead><tr><th>Name</th><th>Brand</th><th>Status</th><th>Price</th><th></th></tr></thead>
  <tbody>
  <?php foreach (get_phones() as $phone): ?>
    <tr>

      <td><?= e($phone['name']) ?></td>
      <td><?= e($phone['brand_name']) ?></td>
      <td><?= $phone['status'] === 'unreleased' ? 'Unreleased' : 'In stock' ?></td>
      <td><?= $phone['price_myr'] ? 'RM ' . number_format((float) $phone['price_myr'], 2) : 'TBA' ?></td>

      <td class="table-actions">
        <a href="?edit=<?= $phone['id'] ?>">Edit</a>

        <form method="post" style="display:inline">
          <input type="hidden" name="delete_id" value="<?= $phone['id'] ?>">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <a href="#" class="danger" onclick="if(confirm('Delete this phone?')){this.closest('form').submit();} return false;">Delete</a>
        </form>

      </td>
    </tr>
    
  <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/footer.php'; ?>