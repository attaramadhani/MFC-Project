<?php
require_once __DIR__ . '/auth.php'; 

$allowedPages = ['dashboard', 'pesanan', 'menu', 'users', 'laporan', 'profile', 'laporan_export'];
$page = $_GET['page'] ?? 'dashboard';
if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}

/**
 * KHUSUS: halaman export PDF
 * - tidak pakai layout admin
 * - tapi tetap lewat index.php supaya auth & $pdo tetap ter-set
 */
if ($page === 'laporan_export') {
    require __DIR__ . '/pages/laporan_export.php';
    exit; // berhenti di sini, jangan lanjut ke header/footer
}

// supaya judul page enak
function page_title($page) {
    switch ($page) {
        case 'pesanan': return 'Kelola Pesanan';
        case 'menu':    return 'Kelola Menu';
        case 'users':   return 'Kelola Pengguna';
        case 'laporan': return 'Laporan Penjualan';
        case 'profile': return 'Profil Admin';
        default:        return 'Dashboard';
    }
}

$title = 'Admin - ' . page_title($page);

include __DIR__ . '/partials/admin_header.php';
?>

<div class="admin-layout">
  <!-- SIDEBAR -->
  <aside class="admin-sidebar">
    <div class="sidebar-brand mb-3">
      <span class="brand-logo">🍗</span>
      <span class="brand-text">GeprekinAja<br><small>Admin Panel</small></span>
    </div>

    <nav class="sidebar-nav">
      <a href="index.php?page=dashboard"
         class="sidebar-link <?= $page === 'dashboard' ? 'active' : '' ?>">
        <span class="icon">📊</span> <span>Dashboard</span>
      </a>
      <a href="index.php?page=pesanan"
         class="sidebar-link <?= $page === 'pesanan' ? 'active' : '' ?>">
        <span class="icon">🧾</span> <span>Pesanan</span>
      </a>
      <a href="index.php?page=menu"
         class="sidebar-link <?= $page === 'menu' ? 'active' : '' ?>">
        <span class="icon">🍽️</span> <span>Menu</span>
      </a>
      <a href="index.php?page=users"
         class="sidebar-link <?= $page === 'users' ? 'active' : '' ?>">
        <span class="icon">👥</span> <span>Pengguna</span>
      </a>
      <a href="index.php?page=laporan"
         class="sidebar-link <?= $page === 'laporan' ? 'active' : '' ?>">
        <span class="icon">📑</span> <span>Laporan</span>
      </a>
      <a href="index.php?page=profile"
         class="sidebar-link <?= $page === 'profile' ? 'active' : '' ?>">
        <span class="icon">⚙️</span> <span>Profil</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="avatar-circle">
          <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
        </div>
        <div class="user-meta">
          <div class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
          <div class="user-role text-muted">Administrator</div>
        </div>
      </div>
      <a href="../logout.php" class="btn btn-sm btn-outline-danger w-100 mt-2">
        Logout
      </a>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="admin-main">
    <div class="admin-main-header">
      <h1 class="admin-page-title"><?= page_title($page) ?></h1>
    </div>

    <div class="admin-main-body">
      <?php
      // load konten sesuai $page
      $file = __DIR__ . '/pages/' . $page . '.php';
      if (file_exists($file)) {
          include $file;
      } else {
          echo '<div class="alert alert-warning">Halaman belum dibuat.</div>';
      }
      ?>
    </div>
  </main>
</div>

<?php
include __DIR__ . '/partials/admin_footer.php';
