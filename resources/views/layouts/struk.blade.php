@php
    $kode = $order->kode_pesanan;
    $tanggal = \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i');
    $totalHarga = (float) $order->total_harga;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 6mm 6mm 6mm 6mm;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #111;
        }

        .receipt-wrapper {
            width: 100%;
            page-break-inside: avoid;
        }

        .receipt-inner {
            max-width: 210px;
            margin: 0 auto;
            padding: 6px 2px 10px;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 8px;
        }

        .resto-name {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .resto-info {
            font-size: 10px;
            line-height: 1.3;
        }

        .divider {
            border-top: 1px dashed #777;
            margin: 6px 0;
        }

        .meta-block {
            font-size: 10px;
        }

        .meta-line {
            display: flex;
            margin-bottom: 1px;
        }

        .meta-label {
            min-width: 52px;
            color: #555;
        }

        .meta-value {
            flex: 1;
            word-break: break-all;
        }

        .section-title {
            font-size: 10px;
            font-weight: 700;
            margin: 2px 0 4px;
        }

        .item-row {
            font-size: 10px;
            margin-bottom: 4px;
        }

        .item-name {
            font-weight: 500;
        }

        .item-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 1px;
        }

        .item-meta-left {
            font-size: 10px;
        }

        .item-meta-right {
            font-size: 10px;
            text-align: right;
            white-space: nowrap;
        }

        .item-note {
            font-size: 9px;
            color: #555;
            margin-top: 1px;
        }

        .total-section {
            margin-top: 4px;
            font-size: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 2px;
        }

        .total-main {
            padding-top: 3px;
            border-top: 1px solid #777;
            font-weight: 700;
            font-size: 11px;
        }

        .footer {
            text-align: center;
            margin-top: 8px;
            font-size: 10px;
            page-break-inside: avoid;
        }

        .footer .thanks {
            font-weight: 700;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
<div class="receipt-wrapper">
  <div class="receipt-inner">

    <div class="receipt-header">
        <div class="resto-name">MFC (Madris Fried Chicken)</div>
        <div class="resto-info">
            Jl. Trunojoyo, Dajahjarad, Banyu Ajuh, Kec. Kamal, Kabupaten Bangkalan, Jawa Timur 69162, tepatnya berada di seberang depan Masjid Al Ihsan<br>
            Telp: +62 857-3112-2725
        </div>
    </div>

    <div class="divider"></div>

    <div class="meta-block">
        <div class="meta-line">
            <div class="meta-label">Kode</div>
            <div class="meta-value">#{{ $kode }}</div>
        </div>
        <div class="meta-line">
            <div class="meta-label">Tanggal</div>
            <div class="meta-value">{{ $tanggal }}</div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="section-title">Rincian Pesanan</div>

        @php
            $groupedItems = [];
            foreach ($items as $row) {
                if ($row->id_menu_paket) {
                    if (!isset($groupedItems['paket_' . $row->id_menu_paket])) {
                        $groupedItems['paket_' . $row->id_menu_paket] = [
                            'is_paket' => true,
                            'nama_paket' => $row->nama_paket,
                            'total_harga' => 0,
                            'komponen' => []
                        ];
                    }
                    $groupedItems['paket_' . $row->id_menu_paket]['komponen'][] = $row;
                    $groupedItems['paket_' . $row->id_menu_paket]['total_harga'] += ($row->harga * $row->jumlah);
                } else {
                    $groupedItems[] = [
                        'is_paket' => false,
                        'item' => $row
                    ];
                }
            }
        @endphp

        @foreach($groupedItems as $group)
            @if($group['is_paket'])
                <div class="item-row" style="font-weight: bold; margin-bottom: 2px;">📦 {{ $group['nama_paket'] }}</div>
                @foreach($group['komponen'] as $k)
                    <div class="item-row" style="padding-left: 10px; color: #555; margin-bottom: 1px;">
                        - {{ $k->nama }} x {{ (int)$k->jumlah }}
                    </div>
                @endforeach
                <div class="item-meta" style="margin-bottom: 4px; font-weight: bold;">
                    <span>Subtotal Paket:</span>
                    <span>Rp {{ number_format($group['total_harga'], 0, ',', '.') }}</span>
                </div>
            @else
                @php
                    $row = $group['item'];
                    $subtotalItem = (float)$row->harga * (int)$row->jumlah;
                @endphp
                <div class="item-row">
                    <div class="item-name">{{ $row->nama }}</div>
                    <div class="item-meta">
                        <div class="item-meta-left">
                            {{ (int)$row->jumlah }} x Rp {{ number_format((float)$row->harga, 0, ',', '.') }}
                        </div>
                        <div class="item-meta-right">
                            Rp {{ number_format($subtotalItem, 0, ',', '.') }}
                        </div>
                    </div>
                    @if(!empty($row->catatan_item))
                        <div class="item-note">Catatan: {{ $row->catatan_item }}</div>
                    @endif
                </div>
            @endif
        @endforeach

    <div class="divider"></div>

    <div class="total-section">
        <div class="total-row total-main">
            <span>Total</span>
            <span>Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
        </div>

        @if (!empty($pembayaran) && !empty($pembayaran->reference))
            <div class="total-row">
                <span>Ref</span>
                <span>{{ $pembayaran->reference }}</span>
            </div>
        @endif
    </div>

    <div class="divider"></div>

    <div class="footer">
        <div class="thanks">Terima kasih!</div>
        <div>Silakan datang kembali.</div>
    </div>

  </div>
</div>
</body>
</html>