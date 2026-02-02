<?php
// admin/pages/profile.php

$id_admin = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->execute([$id_admin]);
$admin = $stmt->fetch();

if (!$admin) {
    echo '<div class="alert alert-danger">Data admin tidak ditemukan.</div>';
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['password_lama'] ?? '';
    $new = $_POST['password_baru'] ?? '';
    $rep = $_POST['password_konfirmasi'] ?? '';

    if ($new === '' || $rep === '') {
        $errorMsg = 'Password baru & konfirmasi wajib diisi.';
    } elseif ($new !== $rep) {
        $errorMsg = 'Konfirmasi password tidak sama.';
    } else {
        $passDb = $admin['pass_user'];

        // dukung hashed / plain lama (sama seperti login)
        $validOld = false;
        if (password_verify($old, $passDb)) {
            $validOld = true;
        } elseif ($old === $passDb) {
            $validOld = true;
        }

        if (!$validOld) {
            $errorMsg = 'Password lama salah.';
        } else {
            $newHash = password_hash($new, PASSWORD_DEFAULT);
            $stmtUp = $pdo->prepare("UPDATE users SET pass_user = ? WHERE id_user = ?");
            $stmtUp->execute([$newHash, $id_admin]);
            $successMsg = 'Password berhasil diganti.';
            // refresh data admin
            $stmt->execute([$id_admin]);
            $admin = $stmt->fetch();
        }
    }
}
?>

<div class="row">
  <div class="col-md-5">
    <div class="stat-card mb-3">
      <h2 class="h5 mb-2">Profil Admin</h2>
      <div class="d-flex align-items-center gap-3 mb-2">
        <div class="avatar-circle" style="width:42px;height:42px;font-size:1.1rem;">
          <?= strtoupper(substr($admin['nama_user'],0,1)) ?>
        </div>
        <div>
          <div class="fw-semibold"><?= htmlspecialchars($admin['nama_user']) ?></div>
          <div class="small text-muted">Role: <?= htmlspecialchars($admin['role']) ?></div>
          <?php if (!empty($admin['created_at'])): ?>
            <div class="small text-muted">
              Bergabung: <?= date('d M Y', strtotime($admin['created_at'])) ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="small text-muted">
        Di sini hanya ada pengaturan dasar akun admin.  
        Untuk mengelola pengguna lain gunakan menu "Pengguna".
      </div>
    </div>
  </div>

  <div class="col-md-7">
    <div class="stat-card">
      <h2 class="h6 mb-2">Ganti Password</h2>

      <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($errorMsg) ?></div>
      <?php endif; ?>
      <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success py-2"><?= htmlspecialchars($successMsg) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-2">
          <label class="form-label small">Password Lama</label>
          <input type="password" name="password_lama" class="form-control form-control-sm" required>
        </div>
        <div class="mb-2">
          <label class="form-label small">Password Baru</label>
          <input type="password" name="password_baru" class="form-control form-control-sm" required>
        </div>
        <div class="mb-3">
          <label class="form-label small">Konfirmasi Password Baru</label>
          <input type="password" name="password_konfirmasi" class="form-control form-control-sm" required>
        </div>
        <button type="submit" class="btn btn-main text-white btn-sm">
          Simpan Perubahan
        </button>
      </form>
    </div>
  </div>
</div>