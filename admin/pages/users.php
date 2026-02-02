<?php
// admin/pages/users.php

// === HANDLE POST ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_POST['action'] ?? '';
    $id_user = (int)($_POST['id_user'] ?? 0);

    if ($id_user > 0) {
        if ($action === 'change_role') {
            $role = $_POST['role'] ?? 'pelanggan';
            if (!in_array($role, ['admin','pelanggan'], true)) {
                $role = 'pelanggan';
            }

            // Jangan turunkan hak admin dirinya sendiri sembarangan (opsional)
            if ($id_user === $_SESSION['user_id'] && $role !== 'admin') {
                $errorMsg = 'Tidak bisa mengubah role akun yang sedang dipakai.';
            } else {
                $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id_user = ?");
                $stmt->execute([$role, $id_user]);
                $successMsg = 'Role pengguna diperbarui.';
            }
        } elseif ($action === 'reset_password') {
            $newPass = '12345'; // default
            $hash    = password_hash($newPass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET pass_user = ? WHERE id_user = ?");
            $stmt->execute([$hash, $id_user]);
            $successMsg = 'Password direset ke "12345".';
        }
    }
}

// === DATA USERS ===
$users = $pdo->query("
  SELECT id_user, nama_user, role, dibuat_pada
  FROM users
  ORDER BY dibuat_pada DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="mb-3">
  <h2 class="h5 mb-1">Kelola Pengguna</h2>
  <div class="text-muted small">
    Ubah role pengguna dan reset password jika diperlukan.
  </div>
</div>

<?php if (!empty($errorMsg)): ?>
  <div class="alert alert-danger py-2"><?= htmlspecialchars($errorMsg) ?></div>
<?php endif; ?>
<?php if (!empty($successMsg)): ?>
  <div class="alert alert-success py-2"><?= htmlspecialchars($successMsg) ?></div>
<?php endif; ?>

<div class="table-responsive">
  <table class="table table-sm align-middle">
    <thead class="table-light">
      <tr>
        <th style="width:60px;">ID</th>
        <th>Nama Pengguna</th>
        <th>Role</th>
        <th>Terdaftar</th>
        <th class="text-end" style="width:200px;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$users): ?>
        <tr>
          <td colspan="5" class="text-center text-muted py-3">Belum ada pengguna.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><?= $u['id_user'] ?></td>
          <td><?= htmlspecialchars($u['nama_user']) ?></td>
          <td>
            <?php if ($u['role'] === 'admin'): ?>
              <span class="badge bg-warning-subtle text-warning">Admin</span>
            <?php else: ?>
              <span class="badge bg-info-subtle text-info">Pelanggan</span>
            <?php endif; ?>
          </td>
          <td>
            <?= $u['dibuat_pada'] ? date('d M Y', strtotime($u['dibuat_pada'])) : '-' ?>
          </td>
          <td class="text-end">
            <!-- Ubah role -->
            <form method="post" class="d-inline-block me-1">
              <input type="hidden" name="action" value="change_role">
              <input type="hidden" name="id_user" value="<?= $u['id_user'] ?>">
              <select name="role" class="form-select form-select-sm d-inline w-auto"
                      onchange="this.form.submit()">
                <option value="pelanggan" <?= $u['role']==='pelanggan'?'selected':'' ?>>Pelanggan</option>
                <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
              </select>
            </form>

            <!-- Reset password -->
            <form method="post" class="d-inline-block"
                  onsubmit="return confirm('Reset password user ini ke 12345?');">
              <input type="hidden" name="action" value="reset_password">
              <input type="hidden" name="id_user" value="<?= $u['id_user'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger">
                Reset Password
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>