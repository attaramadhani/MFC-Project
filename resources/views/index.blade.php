@extends('layouts.app')

@section('content')
    @include('layouts.navbar')

    <div class="container">
      <section id="home" class="mb-5">
        <div class="hero-section">
          <div class="row align-items-center m-0">
            <div class="col-md-6">
                <h1 class="hero-title mb-3">Ayam Geprek Paling Mantap di Bumi!</h1>
                <p class="hero-subtitle mb-4">
                    Pilih menu makanan yang kamu suka, minuman serta tambahan lainnya, dan pesan dengan mudah. Cocok buat anak kos, kantoran, dan keluarga.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center justify-content-md-start">
                    <a href="#menu" class="btn btn-main text-white">Lihat Menu</a>
                    <button type="button"
                        class="btn btn-outline-secondary rounded-pill guest-trigger">
                        Lihat Keranjang
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-center mt-4 mt-md-0">
              <img src="{{ asset('img/bann.webp') }}" class="img-fluid rounded-4 hover-scale" alt="Ayam Geprek">
            </div>
          </div>
        </div>

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

        @foreach ($menusByKategori as $kategori => $listMenu)
          <div class="menu-category-header">
            <h3>{{ $kategori }}</h3>
          </div>


          <div class="row g-4">
            @foreach ($listMenu as $menu)

              <?php
                  $idMenu = (int)$menu['id_menu'];
                  $qty    = isset($cartByMenu[$idMenu]) ? $cartByMenu[$idMenu] : 0;
              ?>
              <div class="col-12 col-sm-6 col-lg-4">
                  <div class="card menu-card h-100">
                  <div class="menu-img-placeholder">
                      @if (!empty($menu->gambar))
                      <img src="{{ get_menu_image_url($menu->gambar) }}" class="w-100 h-100 object-fit-cover" alt="">
                      @endif
                  </div>

                  <div class="card-body menu-card-body d-flex flex-column h-100">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="card-title mb-1" style="font-size:1rem;">
                            <?= htmlspecialchars($menu['nama']) ?>
                            </h5>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                          <span class="badge bg-warning-subtle text-warning-emphasis badge-kategori">
                              <?= htmlspecialchars($menu['kategori']) ?>
                          </span>
                          <?php if (!empty($menu['diskon']) && $menu['diskon'] > 0): ?>
                            <span class="badge bg-danger text-white rounded-pill" style="font-size: 0.7rem;">Diskon <?= $menu['diskon'] ?>%</span>
                          <?php endif; ?>
                        </div>
                      </div>

                      <p class="card-text small text-muted mb-2">
                      <?= nl2br(htmlspecialchars($menu['deskripsi'] ?? '')) ?>
                      </p>

                      <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="small text-muted">
                          <?php if ($menu['stok'] <= 0): ?>
                            <span class="text-danger fw-bold">Stok Habis</span>
                          <?php else: ?>
                            Stok: <span class="fw-semibold text-success"><?= $menu['stok'] ?></span>
                          <?php endif; ?>
                        </span>
                      </div>

                      <div class="menu-card-footer mt-auto">
                        <div class="fw-bold text-orange">
                          <?php if (!empty($menu['diskon']) && $menu['diskon'] > 0): 
                            $finalHarga = $menu['harga'] - ($menu['harga'] * $menu['diskon'] / 100);
                          ?>
                            <span class="text-muted text-decoration-line-through me-1 small" style="font-size: 0.8rem;">
                              Rp <?= number_format($menu['harga'], 0, ',', '.') ?>
                            </span><br>
                            Rp <?= number_format($finalHarga, 0, ',', '.') ?>
                          <?php else: ?>
                            Rp <?= number_format($menu['harga'], 0, ',', '.') ?>
                          <?php endif; ?>
                        </div>

                        <div class="qty-control" data-id="<?= $idMenu ?>">
                          <?php if ($menu['stok'] <= 0): ?>
                            <span class="badge bg-secondary">Habis</span>
                          <?php else: ?>
                            <button class="qty-btn guest-trigger" 
                                    type="button"
                                    data-id="<?= $idMenu ?>">−</button>
                            <span class="qty-value" data-id="<?= $idMenu ?>">0</span>
                            <button class="qty-btn guest-trigger" 
                                    type="button"
                                    data-id="<?= $idMenu ?>">+</button>
                          <?php endif; ?>
                        </div>
                      </div>
                  </div>
                  </div>
              </div>

            @endforeach
          </div>
        @endforeach
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

      <div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content login-required-card">
            <button type="button"
                    class="btn-close login-required-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"></button>

            <div class="login-required-body">
              <div class="login-required-icon">
                🔒
              </div>

              <h5 class="login-required-title mb-1">
                Login dulu ya
              </h5>
              <p class="login-required-subtitle mb-3">
                Biar kamu bisa menambahkan menu ke keranjang dan lanjut checkout.
              </p>

              <div class="d-grid gap-2">
                <a href="{{ route('login') }}" class="btn btn-main text-white w-100">
                  Masuk ke akun
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-secondary rounded-pill w-100">
                  Daftar akun baru
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/index.js') }}"></script>
@endpush