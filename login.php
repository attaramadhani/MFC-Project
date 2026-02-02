<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (isset($_SESSION['user_id'], $_SESSION['role'])) {
  header("Location: " . ($_SESSION['role'] === 'admin' ? "admin/index.php" : "pelanggan/index.php"));
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_pengguna = trim($_POST['nama_pengguna'] ?? '');
  $kata_sandi    = $_POST['kata_sandi'] ?? '';

  $stmt = $pdo->prepare("SELECT id_user, nama_user, pass_user, role FROM users WHERE nama_user = ?");
  $stmt->execute([$nama_pengguna]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $passDb = $user['pass_user'];

    $valid = password_verify($kata_sandi, $passDb) || ($kata_sandi === $passDb);

    if ($valid) {
      $_SESSION['user_id']  = $user['id_user'];
      $_SESSION['username'] = $user['nama_user'];
      $_SESSION['role']     = $user['role'];

      // Optional: security
      session_regenerate_id(true);

      header("Location: " . ($user['role'] === 'admin' ? "admin/index.php" : "pelanggan/index.php"));
      exit;
    }
  }

  $error = "Username atau password salah.";
}

include 'partials/header.php';
?>


<!-- WRAPPER FULL HEIGHT -->
<div class="auth-page">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow-sm border-0 rounded-4 auth-card">
          <div class="card-body p-4 p-md-4">
            <h3 class="mb-3 text-center section-title">Masuk</h3>

            <?php if ($error): ?>
              <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="nama_pengguna" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="kata_sandi" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-main text-white w-100">Masuk</button>
            </form>

            <p class="mt-3 small text-center text-muted">
              Belum punya akun?
              <a href="register.php">Daftar disini</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>