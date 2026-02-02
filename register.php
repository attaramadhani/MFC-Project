<?php
require 'config.php';

// kalau sudah login, langsung lempar sesuai role
if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: pelanggan/index.php");
    }
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pengguna = trim($_POST['nama_pengguna'] ?? '');
    $kata_sandi    = $_POST['kata_sandi'] ?? '';
    $konfirmasi    = $_POST['konfirmasi'] ?? '';

    if ($nama_pengguna === '' || $kata_sandi === '') {
        $error = "Username dan password wajib diisi.";
    } elseif ($kata_sandi !== $konfirmasi) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // cek username di tabel users
        $stmt = $pdo->prepare("SELECT id_user FROM users WHERE nama_user = ?");
        $stmt->execute([$nama_pengguna]);
        $existing = $stmt->fetch();

        if ($existing) {
            $error = "Username sudah digunakan, silakan pilih yang lain.";
        } else {
            $hash = password_hash($kata_sandi, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (nama_user, pass_user, role)
                VALUES (?, ?, 'pelanggan')
            ");
            $stmt->execute([$nama_pengguna, $hash]);

            $id_user = $pdo->lastInsertId();
            $_SESSION['user_id']  = $id_user;
            $_SESSION['username'] = $nama_pengguna;
            $_SESSION['role']     = 'pelanggan';

            $success = "Pendaftaran berhasil. Kamu sudah otomatis login.";
        }
    }
}

include 'partials/header.php';
?>

<!-- HTML FORM REGISTER-KU SESUAI PUNYA KAMU (BISA PAKAI YANG SUDAH ADA) -->
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body p-4">
        <h3 class="mb-3 text-center section-title">Daftar Akun Baru</h3>

        <?php if ($error): ?>
          <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="alert alert-success py-2"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="nama_pengguna" class="form-control"
                   value="<?= isset($nama_pengguna) ? htmlspecialchars($nama_pengguna) : '' ?>"
                   required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="kata_sandi" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="konfirmasi" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-main text-white w-100">Daftar</button>
        </form>

        <p class="mt-3 small text-center text-muted">
          Sudah punya akun?
          <a href="login.php">Masuk di sini</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>