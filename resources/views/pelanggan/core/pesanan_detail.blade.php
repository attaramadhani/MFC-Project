@php
    $paymentStatus = $order->payment_status;
    $orderStatus = $order->order_status;
    $paymentMethod = $order->payment_method ?? 'midtrans';

    $isCompletedAndPaid = ($orderStatus === 'completed' && $paymentStatus === 'paid');

    function humanPaymentStatus($status) {
        switch ($status) {
            case 'paid': return 'Sudah dibayar';
            case 'pending': return 'Menunggu pembayaran';
            case 'failed': return 'Pembayaran gagal';
            case 'expired': return 'Kadaluarsa';
            case 'refunded': return 'Dikembalikan';
            default: return 'Belum dibayar';
        }
    }

    function humanOrderStatus($status) {
        switch ($status) {
            case 'processing': return 'Sedang diproses';
            case 'ready': return 'Diantar';
            case 'completed': return 'Selesai';
            case 'canceled': return 'Dibatalkan';
            default: return 'Dibuat';
        }
    }

    $totalHarga = (float) $order->total_harga;
    $kode = $order->kode_pesanan;
    $tanggal = \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i');
@endphp

<div class="order-detail-wrapper">
  <div class="order-detail-header mb-3">
    <div>
      <div class="small text-muted mb-1">Kode Pesanan</div>
      <div class="fw-semibold">
        {{ $kode }}
      </div>
      <div class="small text-muted">
        {{ $tanggal }}
      </div>
    </div>

    <div class="text-end">
      @if ($orderStatus === 'canceled')
        <div class="small">
          <span class="status-pill badge-soft-red">
            {{ humanOrderStatus($orderStatus) }}
          </span>
        </div>
      @else
        <div class="small mb-1">
          <span class="status-pill badge-soft-gray">
            {{ humanPaymentStatus($paymentStatus) }}
          </span>
        </div>
        <div class="small">
          <span class="status-pill badge-soft-blue">
            {{ humanOrderStatus($orderStatus) }}
          </span>
        </div>
      @endif
    </div>
  </div>

  <div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body">
      <div class="fw-bold mb-2">Info Pengantaran</div>

      <div class="small text-muted mb-1">Metode Pembayaran</div>
      <div class="mb-2">
        {{ $paymentMethod === 'cash' ? 'COD (Bayar di tempat)' : 'Online' }}
      </div>

      <div class="small text-muted mb-1">Alamat Pengantaran</div>
      <div class="mb-2">
        {!! nl2br(e($order->alamat_pengiriman ?? '-')) !!}
      </div>

      <div class="row g-2">
        <div class="col-md-6">
          <div class="small text-muted mb-1">Wilayah</div>
          <div>{{ $order->wilayah_pengiriman ?? '-' }}</div>
        </div>
        <div class="col-md-6">
          <div class="small text-muted mb-1">Ongkir</div>
          <div>Rp {{ number_format((int) ($order->ongkir ?? 0), 0, ',', '.') }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="order-detail-items mb-3">
    @if ($items->isEmpty())
      <div class="text-muted small">
        Tidak ada item pada pesanan ini.
      </div>
    @else
      @foreach ($items as $row)
        @php
          $nama = $row->nama;
          $jumlah = (int) $row->jumlah;
          $harga = (float) $row->harga;
          $subtotal = $jumlah * $harga;
        @endphp

        <div class="order-detail-item">
          <div class="order-detail-item-main">
            <div class="fw-semibold">
              {{ $nama }}
            </div>
            <div class="small text-muted">
              x {{ $jumlah }} • Rp {{ number_format($harga, 0, ',', '.') }}
            </div>
          </div>
          <div class="order-detail-item-subtotal">
            Rp {{ number_format($subtotal, 0, ',', '.') }}
          </div>
        </div>
      @endforeach
    @endif
  </div>

  <div class="order-detail-summary mt-3">
    @php
      $subtotalItems = 0;
      foreach ($items as $row) { $subtotalItems += ($row->jumlah * $row->harga); }
      $ongkirVal = (float) ($order->ongkir ?? 0);
    @endphp
    <div class="card border-0 bg-light rounded-4 mb-3">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between mb-1">
          <span class="text-muted small">Subtotal Menu</span>
          <span class="small">Rp {{ number_format($subtotalItems, 0, ',', '.') }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted small">Ongkos Kirim</span>
          <span class="small">Rp {{ number_format($ongkirVal, 0, ',', '.') }}</span>
        </div>
        <hr class="my-2 opacity-10">
        <div class="d-flex justify-content-between align-items-center">
          <span class="fw-bold small">Total Bayar</span>
          <span class="fw-bold text-danger">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-end">
      <div class="order-detail-summary-left">
        @if ($pembayaran)
          <div class="small text-muted">
            Metode: {{ $paymentMethod === 'cash' ? 'COD' : ($pembayaran->metode ?? $pembayaran->provider) }}<br>
            Status: {{ humanPaymentStatus($pembayaran->status) }}
          </div>
        @else
          <div class="small text-muted">Belum ada data pembayaran.</div>
        @endif
      </div>

    <div class="order-detail-summary-right text-end">
      @php
        $isCanceled = ($orderStatus === 'canceled');
        $isCash = ($paymentMethod === 'cash');
        $canPay = ($paymentStatus !== 'paid') && (!$isCanceled) && (!$isCash);
      @endphp

      @if ($canPay)
        <button
          type="button"
          class="btn btn-main text-white rounded-pill ms-3"
          id="btn-pay-existing"
          data-order-id="{{ $order->id_pesanan }}"
        >
          Lanjutkan Pembayaran
        </button>
        <a href="{{ route('pelanggan.orders.check', $order->id_pesanan) }}" class="btn btn-outline-secondary btn-sm rounded-pill mt-2 d-block ms-auto" style="width: fit-content;">
          <small>Cek Status Pembayaran</small>
        </a>
      @elseif ($isCanceled)
        <div class="small text-danger fw-semibold">
          Pesanan dibatalkan
        </div>
      @elseif ($isCash && $paymentStatus !== 'paid')
        <div class="small text-muted fw-semibold">
          COD — Menunggu konfirmasi admin / bayar saat pesanan diterima.
        </div>
      @else
        <div class="small text-success fw-semibold">
          Pesanan ini sudah dibayar.
        </div>

        @if ($isCompletedAndPaid)
          <button
            type="button"
            class="btn btn-link btn-sm p-0 mt-1 order-detail-receipt-link"
            onclick="window.location.href='{{ route('pelanggan.orders.receipt', $order->id_pesanan) }}';"
          >
            <span class="me-1">🧾</span>
            <span>Unduh struk</span>
          </button>
        @endif
      @endif
    </div>
  </div>
</div>