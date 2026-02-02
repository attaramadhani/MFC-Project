<?php
// pelanggan/cart_content.php
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    echo '<div class="text-center text-muted py-4 small">Silakan login dulu.</div>';
    exit;
}

$id_user = $_SESSION['user_id'];

// ambil isi keranjang + data menu
$stmt = $pdo->prepare("
    SELECT 
        k.id_menu,
        k.jumlah,
        m.nama,
        m.harga
    FROM keranjang k
    JOIN menu m ON m.id_menu = k.id_menu
    WHERE k.id_user = ?
    ORDER BY m.nama
");
$stmt->execute([$id_user]);
$items = $stmt->fetchAll();

if (!$items) {
    ?>
    <div class="cart-empty-state text-center py-4">
        <p class="mb-1 fw-semibold">Keranjang kosong</p>
        <p class="text-muted small mb-0">Silakan pilih dulu menu favoritmu di halaman utama.</p>
    </div>
    <?php
    exit;
}

// hitung total harga
$total_harga = 0;
foreach ($items as $row) {
    $total_harga += $row['jumlah'] * $row['harga'];
}
?>

<div class="cart-modal-wrap">
    <div class="cart-modal-list">
        <?php foreach ($items as $row): 
            $idMenu   = (int)$row['id_menu'];
            $jumlah   = (int)$row['jumlah'];
            $harga    = (int)$row['harga'];
            $subtotal = $jumlah * $harga;
        ?>
        <div class="cart-item" data-row-id="<?= $idMenu ?>">
            <div class="cart-item-left">
                <div class="cart-item-name">
                    <?= htmlspecialchars($row['nama']) ?>
                </div>
                <div
                    class="cart-item-meta"
                    data-id="<?= $idMenu ?>"
                    data-price="<?= $harga ?>"
                >
                    x <?= $jumlah ?> • Rp <?= number_format($harga, 0, ',', '.') ?>
                </div>
            </div>
            <div class="cart-item-right">
                <div class="cart-item-subtotal item-subtotal" data-id="<?= $idMenu ?>">
                    Rp <?= number_format($subtotal, 0, ',', '.') ?>
                </div>
                <div class="qty-control qty-control-sm" data-id="<?= $idMenu ?>">
                    <button
                        type="button"
                        class="qty-btn-modal"
                        data-action="minus"
                        data-id="<?= $idMenu ?>"
                    >−</button>
                    <span class="qty-value" data-id="<?= $idMenu ?>">
                        <?= $jumlah ?>
                    </span>
                    <button
                        type="button"
                        class="qty-btn-modal"
                        data-action="plus"
                        data-id="<?= $idMenu ?>"
                    >+</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="cart-modal-summary">
        <div class="cart-modal-summary-left">
            <div class="cart-modal-total-label">Total</div>
            <div
                class="cart-modal-total-value"
                id="cart-total-display"
            >
                Rp <?= number_format($total_harga, 0, ',', '.') ?>
            </div>
            <div class="cart-modal-summary-text">
                Pembayaran akan diproses dan dikonfirmasi otomatis.
            </div>
        </div>

        <div class="cart-modal-summary-right">
            <button
                type="button"
                class="btn btn-main text-white rounded-pill"
                id="btn-modal-checkout"
            >
                Checkout
            </button>
        </div>
    </div>
</div>