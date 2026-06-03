@extends('layouts.app')

@section('content')
@include('layouts.navbar')

<div class="page-with-navbar mt-5">
  <div class="page-orders">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <h1 class="page-orders-title">Pesanan Saya</h1>
        <p class="page-orders-subtitle">
          Lihat riwayat pesanan, status pembayaran, dan detail menu yang pernah kamu pesan.
        </p>

        @if ($orders->isEmpty())
          <div class="text-center py-5">
            <div class="mb-3">
              <img src="{{ asset('img/logo.jpg') }}" alt="MFC Logo" width="100" class="rounded-4 opacity-75">
            </div>
            <h5 class="mb-2">Belum ada pesanan</h5>
            <p class="text-muted small mb-3">
              Kamu belum pernah melakukan pesanan. Yuk mulai pilih menu favoritmu dulu.
            </p>
            <a href="{{ route('pelanggan.index') }}#menu" class="btn btn-main text-white rounded-pill">
              Lihat Menu
            </a>
          </div>
        @else
          <div class="order-list-wrapper">
            @foreach ($orders as $order)
              @php
                $idPesanan = (int) $order->id_pesanan;
                $kodePesanan = $order->kode_pesanan;
                $totalHarga = (float) $order->total_harga;
                $paymentStatus = $order->payment_status;
                $orderStatus = $order->order_status;
                $createdAt = $order->created_at;
                $totalItem = (int) $order->total_item;

                $tanggalLabel = \Carbon\Carbon::parse($createdAt)->format('d M Y, H:i');

                $badgeBayarClass = 'badge-soft-gray';
                $badgeBayarText = 'Belum dibayar';

                switch ($paymentStatus) {
                    case 'paid':
                        $badgeBayarClass = 'badge-soft-green';
                        $badgeBayarText = 'Sudah dibayar';
                        break;
                    case 'pending':
                        $badgeBayarClass = 'badge-soft-amber';
                        $badgeBayarText = 'Menunggu pembayaran';
                        break;
                    case 'failed':
                    case 'expired':
                    case 'refunded':
                        $badgeBayarClass = 'badge-soft-red';
                        $badgeBayarText = ucfirst($paymentStatus);
                        break;
                    default:
                        $badgeBayarClass = 'badge-soft-gray';
                        $badgeBayarText = 'Belum dibayar';
                }

                $badgeOrderClass = 'badge-soft-gray';
                $badgeOrderText = 'Dibuat';

                switch ($orderStatus) {
                    case 'processing':
                        $badgeOrderClass = 'badge-soft-amber';
                        $badgeOrderText = 'Sedang diproses';
                        break;
                    case 'ready':
                        $badgeOrderClass = 'badge-soft-blue';
                        $badgeOrderText = 'Diantar';
                        break;
                    case 'completed':
                        $badgeOrderClass = 'badge-soft-green';
                        $badgeOrderText = 'Selesai';
                        break;
                    case 'canceled':
                        $badgeOrderClass = 'badge-soft-red';
                        $badgeOrderText = 'Dibatalkan';
                        break;
                    default:
                        $badgeOrderClass = 'badge-soft-gray';
                        $badgeOrderText = 'Dibuat';
                }
              @endphp

              <div class="order-card"
                   data-id="{{ $idPesanan }}"
                   data-bs-toggle="modal"
                   data-bs-target="#orderDetailModal">
                <div class="order-card-left">
                  <div class="order-code-row">
                    <span class="order-code-label">Kode Pesanan</span>
                    <span class="order-code-value">
                      {{ $kodePesanan }}
                    </span>
                  </div>
                  <div class="order-meta">
                    {{ $tanggalLabel }} • {{ $totalItem }} item
                  </div>
                </div>

                <div class="order-card-right">
                  <div class="order-total">
                    Rp {{ number_format($totalHarga, 0, ',', '.') }}
                  </div>

                  <div class="order-badge-row">
                    @if ($orderStatus === 'canceled' || $orderStatus === 'completed')
                      <span class="status-pill {{ $badgeOrderClass }}">
                        {{ $badgeOrderText }}
                      </span>
                    @else
                      <span class="status-pill {{ $badgeBayarClass }}">
                        {{ $badgeBayarText }}
                      </span>
                      <span class="status-pill {{ $badgeOrderClass }}">
                        {{ $badgeOrderText }}
                      </span>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title">Detail Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body" id="orderDetailBody">
        <div class="text-center text-muted py-4 small">
          Memuat detail pesanan...
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalBody = document.getElementById('orderDetailBody');
  const detailModal = document.getElementById('orderDetailModal');
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const orderShowBaseUrl = "{{ url('/pelanggan/pesanan') }}";

  document.querySelectorAll('.order-card').forEach(card => {
    card.addEventListener('click', function () {
      const idPesanan = this.getAttribute('data-id');
      if (!idPesanan || !modalBody) return;

      modalBody.innerHTML = `
        <div class="text-center text-muted py-4 small">
          Memuat detail pesanan...
        </div>
      `;

      fetch(`${orderShowBaseUrl}/${encodeURIComponent(idPesanan)}`, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'text/html'
        }
      })
      .then(res => res.text())
      .then(html => {
        modalBody.innerHTML = html;
      })
      .catch(err => {
        console.error(err);
        modalBody.innerHTML = `
          <div class="text-center text-danger py-4 small">
            Terjadi kesalahan saat memuat detail pesanan.
          </div>
        `;
      });
    });
  });

  if (detailModal) {
    detailModal.addEventListener('click', function (e) {
      const btn = e.target.closest('#btn-pay-existing');
      if (!btn) return;

      const idPesanan = btn.dataset.orderId;
      if (!idPesanan) return;

      fetch('{{ route('pelanggan.checkout.process') }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ id_pesanan: idPesanan })
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          console.error(data);
          alert(data.message || 'Gagal membuat transaksi.');
          return;
        }

        if (!data.token) {
          alert('Token pembayaran tidak ditemukan.');
          return;
        }

        window.snap.pay(data.token, {
          onSuccess: function () {
            window.location.href = "{{ route('pelanggan.orders.check', ':id') }}".replace(':id', idPesanan);
          },
          onPending: function () {
            window.location.href = "{{ route('pelanggan.orders.check', ':id') }}".replace(':id', idPesanan);
          },
          onError: function () {
            alert('Terjadi kesalahan saat pembayaran.');
          },
          onClose: function () {
            // Jika ditutup, coba cek status juga siapa tahu sudah bayar tapi popup ditutup manual
            window.location.href = "{{ route('pelanggan.orders.check', ':id') }}".replace(':id', idPesanan);
          }
        });
      })
      .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan koneksi.');
      });
    });
  }
});
</script>
@endpush