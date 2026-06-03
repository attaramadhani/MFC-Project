@if ($items->isEmpty())
    <div class="cart-empty-state text-center py-4">
        <p class="mb-1 fw-semibold">Keranjang kosong</p>
        <p class="text-muted small mb-0">Silakan pilih dulu menu favoritmu di halaman utama.</p>
    </div>
@else
    <div class="cart-modal-wrap">
        <div class="cart-modal-list">
            @foreach ($items as $row)
                @php
                    $idMenu   = (int) $row->id_menu;
                    $jumlah   = (int) $row->jumlah;
                    $hargaOriginal = (int) $row->harga;
                    $diskonPercent = (int) ($row->diskon ?? 0);
                    $harga    = $hargaOriginal - ($hargaOriginal * $diskonPercent / 100);
                    $subtotal = $jumlah * $harga;
                @endphp

                <div class="cart-item" data-row-id="{{ $idMenu }}">
                    <div class="cart-item-left">
                        <div class="cart-item-name">
                            {{ $row->nama }}
                        </div>
                        <div
                            class="cart-item-meta"
                            data-id="{{ $idMenu }}"
                            data-price="{{ $harga }}"
                        >
                            x {{ $jumlah }} • 
                            @if($diskonPercent > 0)
                                <span class="text-decoration-line-through text-muted small">Rp {{ number_format($hargaOriginal, 0, ',', '.') }}</span>
                            @endif
                            Rp {{ number_format($harga, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="cart-item-right">
                        <div class="cart-item-subtotal item-subtotal" data-id="{{ $idMenu }}">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </div>

                        <div class="qty-control qty-control-sm" data-id="{{ $idMenu }}">
                            <button
                                type="button"
                                class="qty-btn-modal"
                                data-action="minus"
                                data-id="{{ $idMenu }}"
                            >−</button>

                            <span class="qty-value" data-id="{{ $idMenu }}">
                                {{ $jumlah }}
                            </span>

                            <button
                                type="button"
                                class="qty-btn-modal"
                                data-action="plus"
                                data-id="{{ $idMenu }}"
                            >+</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="cart-modal-summary">
            <div class="cart-modal-summary-left">
                <div class="cart-modal-total-label">Total</div>
                <div class="cart-modal-total-value" id="cart-total-display">
                    Rp {{ number_format($total_harga, 0, ',', '.') }}
                </div>
                <div class="cart-modal-summary-text">
                    Pembayaran akan diproses dan dikonfirmasi otomatis.
                </div>
            </div>

            <div class="cart-modal-summary-right">
                <a
                    href="{{ route('pelanggan.checkout') }}"
                    class="btn btn-main text-white rounded-pill"
                    id="btn-modal-checkout"
                >
                    Checkout
                </a>
            </div>
        </div>
    </div>
@endif