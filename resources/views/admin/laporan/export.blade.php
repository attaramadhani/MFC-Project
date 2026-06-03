<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan {{ $from }} s/d {{ $to }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            margin: 25px 30px;
        }
        .header {
            width: 100%;
            display: table;
            margin-bottom: 6px;
        }
        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: top;
        }
        .header-left { width: 60%; padding-right: 10px; }
        .header-right { width: 40%; text-align: right; }
        .brand-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .brand-tagline {
            font-size: 10px;
            font-style: italic;
            color: #555;
            margin-bottom: 4px;
        }
        .brand-info {
            font-size: 9px;
            color: #444;
            line-height: 1.4;
        }
        .report-title {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .report-meta table {
            border-collapse: collapse;
            width: auto;
            margin-left: auto;
        }
        .report-meta td {
            border: none;
            padding: 1px 0 1px 6px;
            font-size: 9px;
        }
        .report-meta td:first-child {
            padding-left: 0;
            font-weight: bold;
        }
        .line-bold {
            border: 0;
            border-top: 2px solid #000;
            margin: 4px 0 1px;
        }
        .line-thin {
            border: 0;
            border-top: 1px solid #000;
            margin: 0 0 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 5px;
        }
        th {
            background: #f0f0f0;
            text-align: center;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary {
            margin-top: 10px;
            font-size: 10px;
        }
        .summary table {
            width: 45%;
            border: none;
        }
        .summary td {
            border: none;
            padding: 2px 0;
        }
        .ttd-wrapper { margin-top: 40px; width:100%; }
        .ttd {
            width: 220px;
            float:right;
            text-align:center;
            font-size:11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <div class="brand-name">MFC (Madris Fried Chicken)</div>
            <div class="brand-tagline">Laporan Operasional & Penjualan</div>
            <div class="brand-info">
                Jl. Trunojoyo, Dajahjarad, Banyu Ajuh, Kec. Kamal, Kabupaten Bangkalan, Jawa Timur 69162, tepatnya berada di seberang depan Masjid Al Ihsan<br>
                Telp: 0857-3112-2725
            </div>
        </div>
        <div class="header-right">
            <div class="report-title">Laporan Penjualan</div>
            <div class="report-meta">
                <table>
                    <tr>
                        <td>Periode</td>
                        <td>:</td>
                        <td>{{ $from }} s/d {{ $to }}</td>
                    </tr>
                    <tr>
                        <td>Dicetak</td>
                        <td>:</td>
                        <td>{{ now()->format('d-m-Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <hr class="line-bold">
    <hr class="line-thin">

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">
                    @if(($filterType ?? 'daily') === 'weekly')
                        Minggu Ke (ISO)
                    @elseif(($filterType ?? 'daily') === 'monthly')
                        Bulan
                    @elseif(($filterType ?? 'daily') === 'yearly')
                        Tahun
                    @else
                        Tanggal
                    @endif
                </th>
                <th style="width: 10%;">Transaksi</th>
                <th style="width: 10%;">Item</th>
                <th>Pendapatan</th>
                <th>Keuntungan Makanan</th>
                <th>Keuntungan Minuman</th>
                <th>Total Keuntungan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $r)
                @php
                    $day_profit = (int)$r->makanan_profit + (int)$r->minuman_profit + (int)$r->tambahan_profit;
                    $periodeDisplay = $r->tgl;
                    if (($filterType ?? 'daily') === 'daily') {
                        $periodeDisplay = \Carbon\Carbon::parse($r->tgl)->format('d M Y');
                    } elseif (($filterType ?? 'daily') === 'monthly') {
                        $periodeDisplay = \Carbon\Carbon::parse($r->tgl . '-01')->translatedFormat('F Y');
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $periodeDisplay }}</td>
                    <td class="text-right">{{ (int) $r->total_transaksi }}</td>
                    <td class="text-right">{{ (int) $r->total_item }}</td>
                    <td class="text-right">Rp {{ number_format((int) $r->total_pendapatan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format((int) $r->makanan_profit, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format((int) $r->minuman_profit, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($day_profit, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>

        @if(count($rows))
        <tfoot>
            <tr>
                <th colspan="2">TOTAL</th>
                <th class="text-right">{{ $totalTransaksi }}</th>
                <th class="text-right">{{ $totalItem }}</th>
                <th class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($totalMakananProfit, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($totalMinumanProfit, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($totalMakananProfit + $totalMinumanProfit + $totalTambahanProfit, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
        @endif
    </table>

    @if(count($rows))
    <div class="summary">
        <table>
            <tr><td>Jumlah hari dengan transaksi</td><td>:</td><td>{{ count($rows) }} hari</td></tr>
            <tr><td>Total transaksi</td><td>:</td><td>{{ $totalTransaksi }} transaksi</td></tr>
            <tr><td>Total item terjual</td><td>:</td><td>{{ $totalItem }} item</td></tr>
            <tr><td>Total pendapatan</td><td>:</td><td>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td></tr>
            <tr><td>Keuntungan Makanan</td><td>:</td><td>Rp {{ number_format($totalMakananProfit, 0, ',', '.') }}</td></tr>
            <tr><td>Keuntungan Minuman</td><td>:</td><td>Rp {{ number_format($totalMinumanProfit, 0, ',', '.') }}</td></tr>
            <tr><td>Total Keuntungan</td><td>:</td><td>Rp {{ number_format($totalMakananProfit + $totalMinumanProfit + $totalTambahanProfit, 0, ',', '.') }}</td></tr>
        </table>
    </div>
    @endif

    <div class="ttd-wrapper">
        <div class="ttd">
            Bangkalan, {{ now()->translatedFormat('d F Y') }}<br>
            Mengetahui,<br><br><br><br>
            <div style="border-top:1px solid #000; padding-top:3px; margin-top:40px; font-weight:bold;">
                {{ $admin }}
            </div>
        </div>
    </div>
</body>
</html>