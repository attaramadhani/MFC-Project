@extends('layouts.admin')

@php
    $pageTitle = 'Laporan Penjualan';
@endphp

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="h5 mb-1">Laporan Penjualan</h2>
        <div class="text-muted small">
            Rekap transaksi berhasil berdasarkan tanggal pembayaran.
        </div>
    </div>

    <a class="btn btn-sm btn-outline-secondary"
       href="{{ route('admin.reports.export', ['filter_type' => $filterType, 'week' => $selectedWeek, 'month' => $selectedMonth, 'year' => $selectedYear]) }}"
       target="_blank">
        Export PDF
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-end" id="filterForm">
            <div class="col-md-4">
                <label class="form-label small mb-1">Tipe Filter</label>
                <select name="filter_type" class="form-select form-select-sm" id="filterTypeSelect" onchange="toggleFilterInputs()">
                    <option value="weekly" {{ $filterType === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="monthly" {{ $filterType === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="yearly" {{ $filterType === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>

            <!-- Input Mingguan -->
            <div class="col-md-5 filter-input-group" id="group-weekly" style="display: none;">
                <label class="form-label small mb-1">Pilih Minggu</label>
                <input type="week" name="week" class="form-control form-control-sm" value="{{ $selectedWeek }}">
            </div>

            <!-- Input Bulanan -->
            <div class="col-md-5 filter-input-group" id="group-monthly" style="display: none;">
                <label class="form-label small mb-1">Pilih Bulan</label>
                <input type="month" name="month" class="form-control form-control-sm" value="{{ $selectedMonth }}">
            </div>

            <!-- Input Tahunan -->
            <div class="col-md-5 filter-input-group" id="group-yearly" style="display: none;">
                <label class="form-label small mb-1">Pilih Tahun</label>
                <select name="year" class="form-select form-select-sm">
                    @for($y = date('Y') - 5; $y <= date('Y'); $y++)
                        <option value="{{ $y }}" {{ (int)$selectedYear === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3">
                <button class="btn btn-sm btn-main text-white w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleFilterInputs() {
        const filterType = document.getElementById('filterTypeSelect').value;
        // Sembunyikan semua input group
        document.querySelectorAll('.filter-input-group').forEach(group => {
            group.style.display = 'none';
            // Disable input agar tidak dikirim di form submit jika tidak aktif
            group.querySelectorAll('input, select').forEach(input => input.disabled = true);
        });

        // Tampilkan yang aktif
        const activeGroup = document.getElementById('group-' + filterType);
        if (activeGroup) {
            activeGroup.style.display = 'block';
            activeGroup.querySelectorAll('input, select').forEach(input => input.disabled = false);
        }
    }

    // Jalankan saat load halaman pertama kali
    document.addEventListener('DOMContentLoaded', function() {
        toggleFilterInputs();
    });
</script>

<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="small text-muted">Makanan Terjual</div>
            <div class="fw-bold">{{ $totalMakanan }} item</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="small text-muted">Minuman Terjual</div>
            <div class="fw-bold">{{ $totalMinuman }} item</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="small text-muted">Tambahan Terjual</div>
            <div class="fw-bold">{{ $totalTambahan }} item</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <!-- Chart menu paling laris -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3 small text-muted text-uppercase">Menu Paling Laris (Top 5)</h6>
                <div style="position: relative; height: 220px; width: 100%;">
                    <canvas id="topMenusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- List / Leaderboard menu paling laris -->
    <div class="col-md-5">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3 small text-muted text-uppercase">Detail Penjualan Terbanyak</h6>
                <div class="d-flex flex-column gap-2">
                    @forelse($topMenus as $i => $tm)
                        @php
                            $colors = ['#ff7675', '#74b9ff', '#55efc4', '#ffeaa7', '#a29bfe'];
                            $color = $colors[$i % count($colors)];
                        @endphp
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(0,0,0,0.02);">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background-color: {{ $color }}; color: #333; font-weight: bold;">
                                    {{ $i + 1 }}
                                </span>
                                <span class="fw-medium text-dark small">{{ $tm->nama_menu }}</span>
                            </div>
                            <span class="badge bg-secondary rounded-pill small">{{ $tm->total_qty }} terjual</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4 small">Belum ada data penjualan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('topMenusChart').getContext('2d');
        const labels = {!! json_encode(collect($topMenus)->pluck('nama_menu')) !!};
        const dataValues = {!! json_encode(collect($topMenus)->pluck('total_qty')) !!};

        if (labels.length === 0) {
            ctx.font = "14px Inter";
            ctx.fillStyle = "#888";
            ctx.textAlign = "center";
            ctx.fillText("Tidak ada data untuk diagram", 150, 110);
            return;
        }

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: dataValues,
                    backgroundColor: [
                        'rgba(255, 118, 117, 0.85)',
                        'rgba(116, 185, 255, 0.85)',
                        'rgba(85, 239, 196, 0.85)',
                        'rgba(255, 234, 167, 0.85)',
                        'rgba(162, 155, 254, 0.85)'
                    ],
                    borderColor: [
                        '#ff7675',
                        '#74b9ff',
                        '#55efc4',
                        '#ffeaa7',
                        '#a29bfe'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 8,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` ${context.parsed.y} porsi/item`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 10
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                family: 'Inter',
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="stat-card" style="border-left: 4px solid #2ecc71;">
            <div class="small text-muted">Keuntungan Makanan</div>
            <div class="fw-bold text-success">Rp {{ number_format($totalMakananProfit, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card" style="border-left: 4px solid #3498db;">
            <div class="small text-muted">Keuntungan Minuman</div>
            <div class="fw-bold text-primary">Rp {{ number_format($totalMinumanProfit, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card" style="border-left: 4px solid #e67e22;">
            <div class="small text-muted">Total Keuntungan</div>
            <div class="fw-bold text-warning-emphasis">Rp {{ number_format($totalMakananProfit + $totalMinumanProfit + $totalTambahanProfit, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="small text-muted">
                    <tr>
                        <th>
                            @if($filterType === 'yearly')
                                Bulan
                            @else
                                Tanggal
                            @endif
                        </th>
                        <th class="text-end">Transaksi</th>
                        <th class="text-end">Total Item</th>
                        <th class="text-end">Pendapatan</th>
                        <th class="text-end">Keuntungan Makanan</th>
                        <th class="text-end">Keuntungan Minuman</th>
                        <th class="text-end">Total Keuntungan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($rows as $r)
                        @php
                            $day_profit = (int)$r->makanan_profit + (int)$r->minuman_profit + (int)$r->tambahan_profit;
                            if ($filterType === 'weekly' || $filterType === 'monthly') {
                                $periodeDisplay = \Carbon\Carbon::parse($r->tgl)->format('d M Y');
                            } elseif ($filterType === 'yearly') {
                                $periodeDisplay = \Carbon\Carbon::parse($r->tgl . '-01')->translatedFormat('F Y');
                            }
                        @endphp
                        <tr>
                            <td>{{ $periodeDisplay }}</td>
                            <td class="text-end">{{ (int) $r->total_transaksi }}</td>
                            <td class="text-end">{{ (int) $r->total_item }}</td>
                            <td class="text-end">Rp {{ number_format((int) $r->total_pendapatan, 0, ',', '.') }}</td>
                            <td class="text-end text-success">Rp {{ number_format((int) $r->makanan_profit, 0, ',', '.') }}</td>
                            <td class="text-end text-primary">Rp {{ number_format((int) $r->minuman_profit, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-success">Rp {{ number_format($day_profit, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Tidak ada transaksi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if(count($rows))
                <tfoot class="small text-muted">
                    <tr>
                        <th>Total</th>
                        <th class="text-end">{{ $totalTransaksi }}</th>
                        <th class="text-end">{{ $totalItem }}</th>
                        <th class="text-end">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</th>
                        <th class="text-end text-success">Rp {{ number_format($totalMakananProfit, 0, ',', '.') }}</th>
                        <th class="text-end text-primary">Rp {{ number_format($totalMinumanProfit, 0, ',', '.') }}</th>
                        <th class="text-end fw-semibold text-success">Rp {{ number_format($totalMakananProfit + $totalMinumanProfit + $totalTambahanProfit, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection