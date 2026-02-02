<?php

// pastikan config & session sudah ada
if (!isset($pdo)) {
    require_once __DIR__ . '/../config.php';
}

// hitung jumlah item & total keranjang (kalau user sudah login)
$cart_count = 0;
$cart_total = 0;

if (isset($_SESSION['user_id'])) {
    $id_user = $_SESSION['user_id'];

    // total item
    $stmtCart = $pdo->prepare("SELECT SUM(jumlah) AS total_item FROM keranjang WHERE id_user = ?");
    $stmtCart->execute([$id_user]);
    $rowCart    = $stmtCart->fetch();
    $cart_count = (int)($rowCart['total_item'] ?? 0);

    // total harga
    $stmtTotal = $pdo->prepare("
        SELECT SUM(k.jumlah * m.harga) AS total_harga
        FROM keranjang k
        JOIN menu m ON m.id_menu = k.id_menu
        WHERE k.id_user = ?
    ");
    $stmtTotal->execute([$id_user]);
    $rowTotal   = $stmtTotal->fetch();
    $cart_total = (int)($rowTotal['total_harga'] ?? 0);
}
?>

    <?php 
    $currentFile = basename($_SERVER['PHP_SELF']);
    if (isset($_SESSION['user_id']) && $currentFile !== 'pesanan.php'): 
    ?>
        <button
            type="button"
            class="floating-cart-btn"
            id="btn-show-cart"
            data-bs-toggle="modal"
            data-bs-target="#cartModal"
        >
            <span class="floating-cart-icon">🛒</span>

            <span
                class="floating-cart-badge<?= $cart_count > 0 ? '' : ' d-none' ?>"
                id="cart-count-badge"
            >
                <?= $cart_count ?>
            </span>
        </button>
    <?php endif; ?>


    <!-- MODAL KERANJANG -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title">Keranjang Pesanan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body" id="cartModalBody">
            <!-- isi keranjang akan dimuat dari pelanggan/cart_content.php via AJAX -->
            <div class="text-center text-muted py-3 small">
              Memuat keranjang...
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    // tentukan path cart.js sesuai lokasi halaman sekarang
    $currentPath = $_SERVER['PHP_SELF'] ?? '';
    if (strpos($currentPath, '/pelanggan/') !== false || strpos($currentPath, '/admin/') !== false) {
        $cartJsPath = '../assets/js/cart.js';
    } else {
        $cartJsPath = 'assets/js/cart.js';
    }
    ?>
    <script src="<?= htmlspecialchars($cartJsPath, ENT_QUOTES, 'UTF-8') ?>"></script>
  </body>
</html>