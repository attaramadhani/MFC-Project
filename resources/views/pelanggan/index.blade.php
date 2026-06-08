@extends('layouts.app')

@section('content')
@include('layouts.navbar')

<div class="container pt-5 mt-5">
    <div class="hero-section">
      <div class="row align-items-center m-0">
        <div class="col-md-6">
            <h1 class="hero-title mb-3">Ayam Crispy Paling Gurih di Sini!</h1>
            <p class="hero-subtitle mb-4">
                Pilih menu makanan yang kamu suka, minuman serta tambahan lainnya, dan pesan dengan mudah. Cocok buat anak kos, kantoran, dan keluarga.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center justify-content-md-start">
                <a href="#menu" class="btn btn-main text-white">Lihat Menu</a>
                <button type="button"
                    class="btn btn-outline-secondary rounded-pill"
                    data-bs-toggle="modal"
                    data-bs-target="#cartModal">
                    Lihat Keranjang
                </button>
            </div>
        </div>
        <div class="col-md-6 text-center mt-4 mt-md-0">
          <img src="{{ asset('img/bann.webp') }}" class="img-fluid rounded-4 hover-scale" alt="Ayam Geprek">
        </div>
      </div>
    </div>

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
      @if(count($menusPaket) > 0)
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="section-title" style="color: var(--red-main);">🌟 Paket Bundling Spesial</h2>
      </div>

      <div class="row g-4 mb-5">
        @foreach ($menusPaket as $menu)
          @php
              $idMenu = (int) $menu->id_menu;
              $qty = $cartByMenu[$idMenu] ?? 0;
          @endphp
          <div class="col-12 col-sm-6 col-lg-4">
            <div class="card menu-card h-100" style="border: 2px solid var(--red-light);">
              <div class="menu-img-placeholder">
                  @if (!empty($menu->gambar))
                      <img src="{{ get_menu_image_url($menu->gambar) }}"
                           class="w-100 h-100 object-fit-cover" alt="">
                  @endif
              </div>

              <div class="card-body menu-card-body d-flex flex-column h-100">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                      <h5 class="card-title mb-1 fw-bold text-danger" style="font-size:1rem;">
                        {{ $menu->nama }}
                      </h5>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                      <span class="badge bg-danger rounded-pill flex-shrink-0" style="font-size: 0.75rem;">
                        Paket
                      </span>
                      @if($menu->diskon > 0)
                        <span class="badge bg-warning text-dark rounded-pill flex-shrink-0" style="font-size: 0.7rem;">Diskon {{ $menu->diskon }}%</span>
                      @endif
                    </div>
                  </div>

                  <p class="card-text small text-muted mb-2">
                    {!! nl2br(e($menu->deskripsi ?? '')) !!}
                  </p>

                  <div class="mb-3 d-flex justify-content-between align-items-center">
                    <span class="small text-muted">
                      @if($menu->stok <= 0)
                        <span class="text-danger fw-bold">Stok Habis</span>
                      @else
                        Stok: <span class="fw-semibold text-success">{{ $menu->stok }}</span>
                      @endif
                    </span>
                  </div>

                  <div class="menu-card-footer mt-auto">
                    <div class="fw-bold text-orange">
                      @if($menu->diskon > 0)
                        @php
                          $finalHarga = $menu->harga - ($menu->harga * $menu->diskon / 100);
                        @endphp
                        <span class="text-muted text-decoration-line-through me-1 small" style="font-size: 0.8rem;">
                          Rp {{ number_format($menu->harga, 0, ',', '.') }}
                        </span><br>
                        Rp {{ number_format($finalHarga, 0, ',', '.') }}
                      @else
                        Rp {{ number_format($menu->harga, 0, ',', '.') }}
                      @endif
                    </div>

                    <div class="qty-control" data-id="{{ $idMenu }}">
                      @if($menu->stok <= 0)
                        <span class="badge bg-secondary">Habis</span>
                      @else
                        <button class="qty-btn" type="button"
                                data-action="minus" data-id="{{ $idMenu }}">−</button>
                        <input
                            type="number"
                            class="qty-input"
                            data-id="{{ $idMenu }}"
                            min="0"
                            max="{{ $menu->stok }}"
                            value="{{ $qty }}"
                            readonly
                        >
                        <button class="qty-btn" type="button"
                                data-action="plus" data-id="{{ $idMenu }}">+</button>
                      @endif
                    </div>
                  </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      @endif

      <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h2 class="section-title">Menu Favorit (Satuan)</h2>
        <span class="text-muted small">Atur jumlah dengan tombol + dan −</span>
      </div>

      @foreach ($menusByKategori as $kategori => $listMenu)
        <div class="menu-category-header">
          <h3>{{ $kategori }}</h3>
        </div>

        <div class="row g-4">
          @foreach ($listMenu as $menu)
            @php
                $idMenu = (int) $menu->id_menu;
                $qty = $cartByMenu[$idMenu] ?? 0;
            @endphp

            <div class="col-12 col-sm-6 col-lg-4">
              <div class="card menu-card h-100">
                <div class="menu-img-placeholder">
                    @if (!empty($menu->gambar))
                        <img src="{{ get_menu_image_url($menu->gambar) }}"
                             class="w-100 h-100 object-fit-cover" alt="">
                    @endif
                </div>

                <div class="card-body menu-card-body d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div>
                        <h5 class="card-title mb-1" style="font-size:1rem;">
                          {{ $menu->nama }}
                        </h5>
                      </div>
                      <div class="d-flex flex-column align-items-end gap-1">
                        <span class="badge bg-warning-subtle text-warning-emphasis badge-kategori">
                          {{ $menu->kategori }}
                        </span>
                        @if($menu->diskon > 0)
                          <span class="badge bg-danger text-white rounded-pill" style="font-size: 0.7rem;">Diskon {{ $menu->diskon }}%</span>
                        @endif
                      </div>
                    </div>

                    <p class="card-text small text-muted mb-2">
                      {!! nl2br(e($menu->deskripsi ?? '')) !!}
                    </p>

                    <div class="mb-3 d-flex justify-content-between align-items-center">
                      <span class="small text-muted">
                        @if($menu->stok <= 0)
                          <span class="text-danger fw-bold">Stok Habis</span>
                        @else
                          Stok: <span class="fw-semibold text-success">{{ $menu->stok }}</span>
                        @endif
                      </span>
                    </div>

                    <div class="menu-card-footer mt-auto">
                      <div class="fw-bold text-orange">
                        @if($menu->diskon > 0)
                          @php
                            $finalHarga = $menu->harga - ($menu->harga * $menu->diskon / 100);
                          @endphp
                          <span class="text-muted text-decoration-line-through me-1 small" style="font-size: 0.8rem;">
                            Rp {{ number_format($menu->harga, 0, ',', '.') }}
                          </span><br>
                          Rp {{ number_format($finalHarga, 0, ',', '.') }}
                        @else
                          Rp {{ number_format($menu->harga, 0, ',', '.') }}
                        @endif
                      </div>

                      <div class="qty-control" data-id="{{ $idMenu }}">
                        @if($menu->stok <= 0)
                          <span class="badge bg-secondary">Habis</span>
                        @else
                          <button class="qty-btn" type="button"
                                  data-action="minus" data-id="{{ $idMenu }}">−</button>

                          <input
                              type="number"
                              class="qty-input"
                              data-id="{{ $idMenu }}"
                              min="0"
                              max="{{ $menu->stok }}"
                              value="{{ $qty }}"
                          >

                          <span class="qty-value d-none" data-id="{{ $idMenu }}">
                              {{ $qty }}
                          </span>

                          <button class="qty-btn" type="button"
                                  data-action="plus" data-id="{{ $idMenu }}">+</button>
                        @endif
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
            <h2 class="section-title mb-3">Tentang MFC (Madris Fried Chicken)</h2>
            <p class="text-muted mb-3">
              MFC (Madris Fried Chicken) adalah usaha ayam goreng crispy yang dibuat supaya kamu bisa pesan makanan favorit
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
                Senin – Minggu: 07.00 – 21.00
              </p>
              <h6 class="mb-2">Lokasi layanan</h6>
              <p class="text-muted small mb-0">
                Jl. Trunojoyo, Dajahjarad, Banyu Ajuh, Kec. Kamal, Kabupaten Bangkalan, Jawa Timur 69162, tepatnya berada di seberang depan Masjid Al Ihsan
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
                <a href="https://wa.me/6285731122725" target="_blank" class="text-decoration-none">
                  +62 857-3112-2725
                </a>
              </div>
            </div>

            <div class="small mb-0">
              <div class="fw-semibold">Alamat</div>
              <div class="text-muted">
                Jl. Trunojoyo, Dajahjarad, Banyu Ajuh, Kec. Kamal, Kabupaten Bangkalan, Jawa Timur 69162, tepatnya berada di seberang depan Masjid Al Ihsan
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
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pelanggan.js') }}?v={{ filemtime(public_path('js/pelanggan.js')) }}"></script>
<script src="{{ asset('js/cart_modal.js') }}?v={{ filemtime(public_path('js/cart_modal.js')) }}"></script>
@endpush