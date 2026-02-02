<?php
require '../config.php';

// WAJIB login
require_login();

// Optional: kalau mau pastiin cuma role pelanggan yang boleh di sini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header('Location: ../login.php');
    exit;
}

// ambil semua menu
$stmt = $pdo->query("SELECT * FROM menu ORDER BY kategori, nama");
$menus = $stmt->fetchAll();

$kategoriMap = [
    'geprek'  => 'Makanan',
    'crispy'  => 'Makanan',
    'gangnam'  => 'Makanan',
    'minuman' => 'Minuman',
    'tambahan' => 'Tambahan',
];

$menusByKategori = [];

foreach ($menus as $m) {
    $raw = strtolower(trim($m['kategori'] ?? 'lainnya'));
    $key = $kategoriMap[$raw] ?? 'Lainnya';
    $menusByKategori[$key][] = $m;
}

$urutanKategori = ['Makanan', 'Minuman', 'Tambahan'];
$orderedMenus = [];

foreach ($urutanKategori as $kat) {
    if (isset($menusByKategori[$kat])) {
        $orderedMenus[$kat] = $menusByKategori[$kat];
    }
}


// ambil data keranjang untuk tahu jumlah per menu
$cartByMenu = [];
$stmtCart = $pdo->prepare("SELECT id_menu, jumlah FROM keranjang WHERE id_user = ?");
$stmtCart->execute([$_SESSION['user_id']]);
foreach ($stmtCart->fetchAll() as $row) {
    $cartByMenu[$row['id_menu']] = (int)$row['jumlah'];
}

include '../partials/header.php';
?>

<!-- HERO FULL SCREEN -->
<div class="hero-section">
  <div class="row align-items-center w-100">
    <div class="col-md-6">
        <h1 class="hero-title mb-3">Ayam Geprek Paling Mantap di Bumi!</h1>
        <p class="hero-subtitle mb-4">
            Pilih menu makanan yang kamu suka, minuman serta tambahan lainnya, dan pesan dengan mudah. Cocok buat anak kos, kantoran, dan keluarga.
        </p>
        <a href="#menu" class="btn btn-main text-white me-2 mb-2">Lihat Menu</a>
        <button type="button"
            class="btn btn-outline-secondary rounded-pill mb-2"
            data-bs-toggle="modal"
            data-bs-target="#cartModal">
            Lihat Keranjang
        </button>
    </div>
    <div class="col-md-6 text-center">
      <img src="../assets/img/bann.webp" 
           class="img-fluid rounded-4" alt="Ayam Geprek">
    </div>
  </div>
</div>

<!-- SECTION KEUNGGULAN -->
<section class="mb-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="bg-white rounded-4 shadow-sm p-3 h-100">
        <div class="mb-2 fs-3">⚡</div>
        <h5 class="mb-2">Cepat & Praktis</h5>
        <p class="small text-muted mb-0">
          Pesan lewat web, tanpa ribet. Tinggal pilih menu, bayar cashless, tinggal jemput sat-set.
        </p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="bg-white rounded-4 shadow-sm p-3 h-100">
        <div class="mb-2 fs-3">🌶️</div>
        <h5 class="mb-2">Sambal Lezat</h5>
        <p class="small text-muted mb-0">
          Racikan sambal geprek yg dibuat dengan cita rasa.
        </p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="bg-white rounded-4 shadow-sm p-3 h-100">
        <div class="mb-2 fs-3">🧊</div>
        <h5 class="mb-2">Fresh & Higienis</h5>
        <p class="small text-muted mb-0">
          Sambal dibuat fresh, dapur terjaga, jadi kamu makan tanpa rasa was-was.
        </p>
      </div>
    </div>
  </div>
</section>

<hr class="mb-5">

<section id="menu" class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="section-title">Menu Favorit</h2>
    <span class="text-muted small">Atur jumlah dengan tombol + dan −</span>
  </div>

  <?php foreach ($menusByKategori as $kategori => $listMenu): ?>
    <div class="menu-category-header">
      <h3><?= htmlspecialchars($kategori) ?></h3>
    </div>


    <div class="row g-4">
      <?php foreach ($listMenu as $menu): ?>

        <?php
            $idMenu = (int)$menu['id_menu'];
            $qty    = isset($cartByMenu[$idMenu]) ? $cartByMenu[$idMenu] : 0;
        ?>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card menu-card h-100">
            <div class="menu-img-placeholder">
                <?php if (!empty($menu['gambar'])): ?>
                <img src="../assets/img/<?= htmlspecialchars($menu['gambar']) ?>" 
                    class="w-100 h-100 object-fit-cover" alt="">
                <?php endif; ?>
            </div>

            <div class="card-body menu-card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h5 class="card-title mb-1" style="font-size:1rem;">
                    <?= htmlspecialchars($menu['nama']) ?>
                    </h5>
                </div>
                <span class="badge bg-warning-subtle text-warning-emphasis badge-kategori">
                    <?= htmlspecialchars($menu['kategori']) ?>
                </span>
                </div>

                <p class="card-text small text-muted mb-3">
                <?= nl2br(htmlspecialchars($menu['deskripsi'] ?? '')) ?>
                </p>

                <div class="menu-card-footer">
                <div class="fw-bold text-orange">
                    Rp <?= number_format($menu['harga'], 0, ',', '.') ?>
                </div>

                    <div class="qty-control" data-id="<?= $idMenu ?>">
                        <button class="qty-btn" type="button"
                                data-action="minus" data-id="<?= $idMenu ?>">−</button>

                        <input
                            type="number"
                            class="qty-input"
                            data-id="<?= $idMenu ?>"
                            min="0"
                            max="99"
                            value="<?= $qty ?>"
                        >

                        <span class="qty-value d-none" data-id="<?= $idMenu ?>">
                            <?= $qty ?>
                        </span>

                        <button class="qty-btn" type="button"
                                data-action="plus" data-id="<?= $idMenu ?>">+</button>
                    </div>
                </div>
            </div>
            </div>
        </div>

      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>


</section>

<section id="about" class="mb-5">
  <div class="rounded-4 bg-white shadow-sm p-4 p-md-5">
    <div class="row g-4 align-items-center">
      <div class="col-lg-6">
        <h2 class="section-title mb-3">Tentang GeprekinAja</h2>
        <p class="text-muted mb-3">
          GeprekinAja adalah usaha ayam geprek yang dibuat supaya kamu bisa pesan makanan favorit
          dengan cara yang sederhana. Semua menu diracik dengan resep yang sama setiap hari,
          jadi rasa yang kamu dapatkan selalu konsisten.
        </p>
        <p class="text-muted mb-0">
          Website ini kami bangun agar proses pesan, pilih menu, dan pembayaran terasa seperti
          aplikasi yang kamu pakai sehari-hari, tapi tetap ringan dan mudah diakses.
        </p>
      </div>
      <div class="col-lg-6">
        <div class="border rounded-4 p-3 p-md-4 h-100">
          <h6 class="mb-2">Jam buka</h6>
          <p class="text-muted small mb-3">
            Senin – Minggu: 07.00 – 21.00<br>
          </p>
          <h6 class="mb-2">Lokasi layanan</h6>
          <p class="text-muted small mb-0">
            Jl. Trunojoyo No.28, Banyu Ajuh, Kec. Kamal, Kabupaten Bangkalan, Jawa Timur 69162
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="contact" class="mb-5">
  <div class="rounded-4 bg-white shadow-sm p-4 p-md-5">
    <div class="row g-4">
      <div class="col-lg-6">
        <h2 class="section-title mb-3">Kontak</h2>
        <p class="text-muted small mb-3">
          Butuh bantuan, pertanyaan, atau ingin kerja sama, bisa hubungi kami lewat kontak ini.
        </p>
        <div class="small mb-2">
          <div class="fw-semibold">WhatsApp</div>
          <div class="text-muted">
            <a href="https://wa.me/6281234567890" target="_blank" class="text-decoration-none">
              +62 896-6798-1666
            </a>
          </div>
        </div>
        <div class="small mb-2">
          <div class="fw-semibold">Email</div>
          <div class="text-muted">
            geprekin@gmail.com
          </div>
        </div>
        <div class="small mb-0">
          <div class="fw-semibold">Alamat</div>
          <div class="text-muted">
            Jl. Trunojoyo No.28 (Depan SPBU) Kec. Kamal, Kabupaten Bangkalan
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="border rounded-4 p-3 p-md-4 h-100 d-flex align-items-center">
          <p class="text-muted small mb-0">
            Untuk saat ini pemesanan utama tetap melalui website ini. 
            Jika ada kendala saat proses pesanan atau pembayaran, 
            bisa langsung chat WhatsApp di jam operasional.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyButtons = document.querySelectorAll('.qty-btn');
    const qtyInputs  = document.querySelectorAll('.qty-input');
    const cartBadge  = document.getElementById('cart-count-badge');

    // helper: sync tampilan span & input
    function syncQtyDisplay(idMenu, newQty) {
        const span  = document.querySelector('.qty-value[data-id="' + idMenu + '"]');
        const input = document.querySelector('.qty-input[data-id="' + idMenu + '"]');

        if (span)  span.textContent  = newQty;
        if (input) input.value       = newQty;
    }

    // helper: update badge mengambang
    function updateBadge(cartCount) {
        if (!cartBadge) return;
        const c = parseInt(cartCount || 0, 10);
        cartBadge.textContent = c;

        if (c > 0) {
            cartBadge.classList.remove('d-none');
            cartBadge.classList.add('badge-bump');
            setTimeout(() => cartBadge.classList.remove('badge-bump'), 250);
        } else {
            cartBadge.classList.add('d-none');
        }
    }

    // helper: kirim request plus / minus
    function sendUpdate(idMenu, action, cb) {
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id_menu=' + encodeURIComponent(idMenu) +
                  '&action=' + encodeURIComponent(action)
        })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success) {
                console.error('Gagal update cart:', data);
                return;
            }

            syncQtyDisplay(idMenu, data.new_qty);
            updateBadge(data.cart_count);

            if (typeof cb === 'function') cb(data);
        })
        .catch(err => console.error(err));
    }

    // ====== PLUS / MINUS TOMBOL ======
    qtyButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const idMenu = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');
            if (!idMenu || !action) return;

            this.disabled = true;
            sendUpdate(idMenu, action, () => {
                this.disabled = false;
            });
        });
    });

    // ====== INPUT ANGKA (KETIK JUMLAH LANGSUNG) ======
    qtyInputs.forEach(input => {
        // simpan nilai lama saat fokus
        input.addEventListener('focus', function () {
            this.dataset.prev = this.value;
        });

        input.addEventListener('change', function () {
            const idMenu = this.dataset.id;
            if (!idMenu) return;

            const prev = parseInt(this.dataset.prev || '0', 10) || 0;
            let target = parseInt(this.value, 10);

            if (isNaN(target) || target < 0) target = 0;
            if (target > 99) target = 99;

            this.value = target; // normalisasi tampilan

            if (target === prev) return;

            const diff   = target - prev;
            const action = diff > 0 ? 'plus' : 'minus';
            const times  = Math.abs(diff);

            // batasi supaya tidak spam server
            const maxTimes = Math.min(times, 50);
            let count = 0;

            function step() {
                if (count >= maxTimes) return;
                sendUpdate(idMenu, action, function () {
                    count++;
                    if (count < maxTimes) {
                        step();
                    }
                });
            }

            step();
        });
    });
});
</script>

<?php include '../partials/footer.php'; ?>