@extends('layouts.app')

@section('content')
{{-- @include('layouts.navbar') --}}

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <h3 class="section-title mb-3">Review Pesanan</h3>

      @if ($items->isEmpty())
        <div class="alert alert-warning">
          Keranjangmu masih kosong. Silakan pilih menu dulu.
        </div>
      @else

        <div class="card border-0 shadow-sm rounded-4 mb-4">
          <div class="card-body">
            <h5 class="mb-3">Checkout</h5>

            <div class="mb-3">
              <label class="form-label">Alamat Pengantaran (kost/jalan/kamar)</label>
              <textarea id="alamat_pengiriman" class="form-control" rows="2"
                placeholder="Contoh: Kost Melati, Jl. Trunojoyo, Kamar 3"></textarea>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">Wilayah</label>
                <select id="wilayah_pengiriman" class="form-select">
                  <option value="">-- Pilih Wilayah --</option>
                  <option value="Kamal">Kamal</option>
                  <option value="Telang">Telang</option>
                </select>
                <div class="form-text">Ongkir dihitung berdasarkan wilayah.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Metode Pembayaran</label>

                <div class="form-check">
                  <input class="form-check-input" type="radio" name="payment_method"
                        id="pay_midtrans" value="midtrans" checked>
                  <label class="form-check-label" for="pay_midtrans">
                    QRIS / E-Wallet
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="radio" name="payment_method"
                        id="pay_cash" value="cash">
                  <label class="form-check-label" for="pay_cash">
                    Cash (COD)
                  </label>
                </div>
              </div>
            </div>

            <hr class="my-3">

            <h6 class="mb-2">Ringkasan Pesanan</h6>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th>Menu</th>
                    <th class="text-center" style="width:90px;">Jumlah</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($items as $item)
                    <tr>
                      <td>{{ $item->nama }}</td>
                      <td class="text-center">{{ (int) $item->jumlah }}</td>
                      <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                      <td>Rp {{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <hr class="my-3">

            <div class="d-flex justify-content-between">
              <div class="text-muted">Subtotal</div>
              <div class="fw-semibold">Rp {{ number_format($total, 0, ',', '.') }}</div>
            </div>

            <div class="d-flex justify-content-between">
              <div class="text-muted">Ongkir</div>
              <div class="fw-semibold" id="ongkir_text">Rp 0</div>
            </div>

            <div class="d-flex justify-content-between mt-2 pt-2 border-top">
              <div class="fw-bold">Total Bayar</div>
              <div class="fw-bold" id="grand_total_text">Rp {{ number_format($total, 0, ',', '.') }}</div>
            </div>
          </div>
        </div>

        <div class="text-end">
          <button class="btn btn-main text-white" id="btn-snap-pay">Bayar Sekarang</button>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const payBtn = document.getElementById('btn-snap-pay');
  if (!payBtn) return;

  const alamatEl = document.getElementById('alamat_pengiriman');
  const wilayahEl = document.getElementById('wilayah_pengiriman');
  const ongkirTextEl = document.getElementById('ongkir_text');
  const grandTotalEl = document.getElementById('grand_total_text');
  const token = document.querySelector('meta[name="csrf-token"]');

  const ONGKIR_MAP = {
    Kamal: 1000,
    Telang: 3000
  };

  const subtotal = {{ (int) $total }};

  function formatRupiah(n) {
    return 'Rp ' + (n || 0).toLocaleString('id-ID');
  }

  function refreshTotalUI() {
    const wilayah = wilayahEl ? wilayahEl.value : '';
    const ongkir = ONGKIR_MAP[wilayah] || 0;

    if (ongkirTextEl) ongkirTextEl.textContent = formatRupiah(ongkir);
    if (grandTotalEl) grandTotalEl.textContent = formatRupiah(subtotal + ongkir);
  }

  if (wilayahEl) wilayahEl.addEventListener('change', refreshTotalUI);
  refreshTotalUI();

  payBtn.addEventListener('click', function () {
    const alamat = alamatEl ? alamatEl.value.trim() : '';
    const wilayah = wilayahEl ? wilayahEl.value : '';
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'midtrans';

    if (!alamat) {
      alert('Alamat pengantaran wajib diisi.');
      return;
    }

    if (!wilayah) {
      alert('Silakan pilih wilayah.');
      return;
    }


    fetch('{{ route('pelanggan.checkout.process') }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token ? token.getAttribute('content') : '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            alamat_pengiriman: alamat,
            wilayah_pengiriman: wilayah,
            payment_method: paymentMethod
        })
        })
        .then(async (res) => {
        const text = await res.text();
        console.log('CHECKOUT STATUS:', res.status);
        console.log('CHECKOUT RESPONSE:', text);

        try {
            return JSON.parse(text);
        } catch (e) {
            throw new Error('Response checkout bukan JSON');
        }
        })
        .then(data => {
        if (!data.success) {
            console.error(data);
            alert(data.message || 'Gagal membuat transaksi');
            return;
        }

        if (data.mode === 'cash') {
            alert('Pesanan COD berhasil dibuat.');
            window.location.href = '{{ route('pelanggan.orders.index') }}';
            return;
        }

        if (!data.token) {
            alert('Token pembayaran tidak ditemukan.');
            return;
        }

        window.snap.pay(data.token, {
            onSuccess: function () {
            window.location.href = '{{ route('pelanggan.orders.index') }}';
            },
            onPending: function () {
            window.location.href = '{{ route('pelanggan.orders.index') }}';
            },
            onError: function () {
            alert('Terjadi kesalahan saat pembayaran.');
            }
        });
        })
        .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan koneksi.');
        });
  });
});
</script>
@endpush