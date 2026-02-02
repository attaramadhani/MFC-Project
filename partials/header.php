<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>GeprekinAja</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/geprekin/assets/css/style.css">
</head>
<?php
$bodyClass = isset($page) ? 'page-' . $page : '';
?>
<body class="<?= htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') ?>">
    <nav class="navbar navbar-expand-lg fixed-top navbar-blur">
  <div class="container">
    <a class="navbar-brand" href="index.php">GeprekinAja</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav nav-main mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="index.php#menu">Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/restoran/pelanggan/pesanan.php">Pesanan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#about">Tentang</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#contact">Kontak</a>
        </li>
      </ul>

      <!-- NAV KANAN -->
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item me-2 d-none d-lg-block">
            <?php
              $uname   = $_SESSION['username'] ?? 'User';
              $initial = mb_strtoupper(mb_substr($uname, 0, 1));
            ?>
            <div class="user-chip">
              <div class="user-avatar"><?= htmlspecialchars($initial) ?></div>
              <span><?= htmlspecialchars($uname) ?></span>
            </div>
          </li>
          <li class="nav-item">
            <a href="../logout.php" class="btn btn-sm btn-outline-secondary rounded-pill">
              Logout
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a href="login.php" class="btn btn-main text-white btn-sm rounded-pill">
              Login
            </a>
          </li>
        <?php endif; ?> 
      </ul>
    </div>
  </div>
</nav>

<!-- Container umum -->
<div class="container mt-5 pt-4">