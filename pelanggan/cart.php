<?php
// cart.php - halaman penuh untuk melihat keranjang
require 'config.php';
require_login();

include '../partials/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <h3 class="mb-4 section-title">Keranjang Belanja</h3>
      <div id="cartPageContent">
        <?php include 'cart_content.php'; ?>
      </div>
      <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="index.php" class="btn btn-outline-secondary">
          ← Kembali ke menu
        </a>
        <a href="checkout.php" class="btn btn-main text-white">
          Lanjut ke Pembayaran
        </a>
      </div>
    </div>
  </div>
</div>

<?php include '../partials/footer.php'; ?>