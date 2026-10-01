<?php

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id']) && (int) $_POST['delete_id'] !== current_user()['id']) {
        delete_user((int) $_POST['delete_id']);
    } 
    elseif (isset($_POST['user_id'], $_POST['role_id'])) {
        update_user_role((int) $_POST['user_id'], (int) $_POST['role_id']);
    }

    header('Location: ' . app_url('users.php'));
    exit;
}

$pageTitle = 'Manage users — PhonePulse';
require __DIR__ . '/header.php';
$roles = get_roles();
?>

<h1>Users &amp; roles</h1>
<p class="sub">Assign each account a role. Admins can manage everything; salemen manage the catalogue; viewers browse and favourite.</p>

<table>

  <thead><tr><th>Username</th><th>Role</th><th>Joined</th><th></th></tr></thead>
  <tbody>
  <?php foreach (get_users() as $u): ?>
    <tr>
      <td><?= e($u['username']) ?></td>

      <td>
        <form method="post" style="display:flex; gap:8px; align-items:center;">
          <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
          <select name="role_id" onchange="this.form.submit()">
            <?php foreach ($roles as $role): ?>
              <option value="<?= $role['id'] ?>" <?= $role['name'] === $u['role'] ? 'selected' : '' ?>><?= e($role['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </td>

      <td><?= e($u['created_at']) ?></td>
      <td class="table-actions">
        <?php if ($u['username'] !== current_user()['username']): ?>
        <form method="post" style="display:inline">
          <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
          <a href="#" class="danger" onclick="if(confirm('Remove this user?')){this.closest('form').submit();} return false;">Remove</a>
        </form>
        <?php endif; ?>
      </td>
    </tr>
    
  <?php endforeach; ?>
  </tbody>

</table>
<?php require __DIR__ . '/footer.php'; ?>