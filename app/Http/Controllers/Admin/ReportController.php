<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;

class ReportController extends Controller
{
    public function index()
    {
        $filterType = request('filter_type', 'weekly'); // default to weekly as requested
        $selectedWeek = request('week', now()->format('Y-\Ww')); // e.g. "2026-W23" or similar format for input type="week"
        $selectedMonth = request('month', now()->format('Y-m')); // e.g. "2026-06"
        $selectedYear = request('year', now()->format('Y')); // e.g. "2026"

        $dateSelect = "CAST(p.paid_at AS DATE) AS tgl";
        $groupBy = "CAST(p.paid_at AS DATE)";
        $orderBy = "tgl ASC";
        $whereClause = "";
        $bindings = [];

        if ($filterType === 'weekly') {
            // Kita filter berdasarkan format tahun-minggu: 'YYYY-"W"IW'
            // Format input type="week" biasanya "YYYY-Www" (contoh: "2026-W23" atau "2026-W09").
            // Untuk PostgreSQL kita bisa format pembandingnya dengan TO_CHAR(p.paid_at, 'IYYY-"W"IW')
            // Catatan: IYYY mendefinisikan tahun ISO.
            $dateSelect = "CAST(p.paid_at AS DATE) AS tgl";
            $groupBy = "CAST(p.paid_at AS DATE)";
            $orderBy = "tgl ASC";
            
            // Konversi format week "2026-W23" ke "2026-W23"
            $formattedWeek = str_replace('-W', '-W', $selectedWeek);
            // Tambahkan padding jika digit minggu hanya satu angka (e.g. W9 -> W09)
            // Tapi input browser type="week" selalu menghasilkan 2 digit (e.g. 2026-W23 atau 2026-W03).
            $whereClause = "TO_CHAR(p.paid_at, 'IYYY-\"W\"IW') = ?";
            $bindings[] = $formattedWeek;
        } elseif ($filterType === 'monthly') {
            $dateSelect = "CAST(p.paid_at AS DATE) AS tgl";
            $groupBy = "CAST(p.paid_at AS DATE)";
            $orderBy = "tgl ASC";
            $whereClause = "TO_CHAR(p.paid_at, 'YYYY-MM') = ?";
            $bindings[] = $selectedMonth;
        } elseif ($filterType === 'yearly') {
            // Untuk tahunan, kita group by bulan 'YYYY-MM'
            $dateSelect = "TO_CHAR(p.paid_at, 'YYYY-MM') AS tgl";
            $groupBy = "TO_CHAR(p.paid_at, 'YYYY-MM')";
            $orderBy = "tgl ASC";
            $whereClause = "TO_CHAR(p.paid_at, 'YYYY') = ?";
            $bindings[] = $selectedYear;
        }

        $rows = DB::select("
            SELECT 
                {$dateSelect},
                COUNT(p.id_pesanan) AS total_transaksi,
                SUM(p.total_harga) AS total_pendapatan,
                SUM(oi.total_item) AS total_item,
                SUM(oi.makanan_qty) AS makanan_qty,
                SUM(oi.minuman_qty) AS minuman_qty,
                SUM(oi.tambahan_qty) AS tambahan_qty,
                SUM(oi.makanan_profit) AS makanan_profit,
                SUM(oi.minuman_profit) AS minuman_profit,
                SUM(oi.tambahan_profit) AS tambahan_profit
            FROM pesanan p
            JOIN (
                SELECT 
                    dp.id_pesanan,
                    SUM(dp.jumlah) AS total_item,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam','makanan') THEN dp.jumlah ELSE 0 END) AS makanan_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah ELSE 0 END) AS minuman_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) NOT IN ('geprek','crispy','gangnam','makanan','minuman') THEN dp.jumlah ELSE 0 END) AS tambahan_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam','makanan') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS makanan_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS minuman_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) NOT IN ('geprek','crispy','gangnam','makanan','minuman') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS tambahan_profit
                FROM detail_pesanan dp
                JOIN menu m ON m.id_menu = dp.id_menu
                GROUP BY dp.id_pesanan
            ) oi ON oi.id_pesanan = p.id_pesanan
            WHERE p.payment_status = 'paid'
              AND {$whereClause}
            GROUP BY {$groupBy}
            ORDER BY {$orderBy}
        ", $bindings);

        $totalPendapatan = 0;
        $totalTransaksi = 0;
        $totalItem = 0;
        $totalMakanan = 0;
        $totalMinuman = 0;
        $totalTambahan = 0;
        $totalMakananProfit = 0;
        $totalMinumanProfit = 0;
        $totalTambahanProfit = 0;

        foreach ($rows as $r) {
            $totalPendapatan += (int) $r->total_pendapatan;
            $totalTransaksi += (int) $r->total_transaksi;
            $totalItem += (int) $r->total_item;
            $totalMakanan += (int) $r->makanan_qty;
            $totalMinuman += (int) $r->minuman_qty;
            $totalTambahan += (int) $r->tambahan_qty;
            $totalMakananProfit += (int) $r->makanan_profit;
            $totalMinumanProfit += (int) $r->minuman_profit;
            $totalTambahanProfit += (int) $r->tambahan_profit;
        }

        // Query item menu paling banyak dibeli
        $topMenus = DB::select("
            SELECT 
                m.nama AS nama_menu,
                SUM(dp.jumlah) as total_qty
            FROM detail_pesanan dp
            JOIN menu m ON m.id_menu = dp.id_menu
            JOIN pesanan p ON p.id_pesanan = dp.id_pesanan
            WHERE p.payment_status = 'paid'
              AND {$whereClause}
            GROUP BY m.id_menu, m.nama
            ORDER BY total_qty DESC
            LIMIT 5
        ", $bindings);

        $transactions = DB::select("
            SELECT 
                p.id_pesanan,
                p.kode_pesanan,
                p.paid_at,
                p.total_harga,
                p.payment_method,
                u.nama_user AS nama_pelanggan,
                oi.total_item,
                oi.item_details,
                (oi.makanan_profit + oi.minuman_profit + oi.tambahan_profit) AS total_profit
            FROM pesanan p
            LEFT JOIN users u ON u.id_user = p.id_user
            LEFT JOIN (
                SELECT 
                    dp.id_pesanan,
                    SUM(dp.jumlah) AS total_item,
                    STRING_AGG(m.nama || ' (' || dp.jumlah || 'x)', ', ') AS item_details,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam','makanan') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS makanan_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS minuman_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) NOT IN ('geprek','crispy','gangnam','makanan','minuman') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS tambahan_profit
                FROM detail_pesanan dp
                JOIN menu m ON m.id_menu = dp.id_menu
                GROUP BY dp.id_pesanan
            ) oi ON oi.id_pesanan = p.id_pesanan
            WHERE p.payment_status = 'paid'
              AND {$whereClause}
            ORDER BY p.paid_at DESC
        ", $bindings);

        return view('admin.laporan.index', compact(
            'rows',
            'filterType',
            'selectedWeek',
            'selectedMonth',
            'selectedYear',
            'totalPendapatan',
            'totalTransaksi',
            'totalItem',
            'totalMakanan',
            'totalMinuman',
            'totalTambahan',
            'totalMakananProfit',
            'totalMinumanProfit',
            'totalTambahanProfit',
            'topMenus',
            'transactions'
        ));
    }

    public function export()
    {
        $filterType = request('filter_type', 'weekly');
        $selectedWeek = request('week', now()->format('Y-\Ww'));
        $selectedMonth = request('month', now()->format('Y-m'));
        $selectedYear = request('year', now()->format('Y'));

        $dateSelect = "CAST(p.paid_at AS DATE) AS tgl";
        $groupBy = "CAST(p.paid_at AS DATE)";
        $orderBy = "tgl ASC";
        $whereClause = "";
        $bindings = [];

        if ($filterType === 'weekly') {
            $dateSelect = "CAST(p.paid_at AS DATE) AS tgl";
            $groupBy = "CAST(p.paid_at AS DATE)";
            $orderBy = "tgl ASC";
            $formattedWeek = str_replace('-W', '-W', $selectedWeek);
            $whereClause = "TO_CHAR(p.paid_at, 'IYYY-\"W\"IW') = ?";
            $bindings[] = $formattedWeek;
        } elseif ($filterType === 'monthly') {
            $dateSelect = "CAST(p.paid_at AS DATE) AS tgl";
            $groupBy = "CAST(p.paid_at AS DATE)";
            $orderBy = "tgl ASC";
            $whereClause = "TO_CHAR(p.paid_at, 'YYYY-MM') = ?";
            $bindings[] = $selectedMonth;
        } elseif ($filterType === 'yearly') {
            $dateSelect = "TO_CHAR(p.paid_at, 'YYYY-MM') AS tgl";
            $groupBy = "TO_CHAR(p.paid_at, 'YYYY-MM')";
            $orderBy = "tgl ASC";
            $whereClause = "TO_CHAR(p.paid_at, 'YYYY') = ?";
            $bindings[] = $selectedYear;
        }

        $rows = DB::select("
            SELECT 
                {$dateSelect},
                COUNT(p.id_pesanan) AS total_transaksi,
                SUM(p.total_harga) AS total_pendapatan,
                SUM(oi.total_item) AS total_item,
                SUM(oi.makanan_qty) AS makanan_qty,
                SUM(oi.minuman_qty) AS minuman_qty,
                SUM(oi.tambahan_qty) AS tambahan_qty,
                SUM(oi.makanan_profit) AS makanan_profit,
                SUM(oi.minuman_profit) AS minuman_profit,
                SUM(oi.tambahan_profit) AS tambahan_profit
            FROM pesanan p
            JOIN (
                SELECT 
                    dp.id_pesanan,
                    SUM(dp.jumlah) AS total_item,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam','makanan') THEN dp.jumlah ELSE 0 END) AS makanan_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah ELSE 0 END) AS minuman_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) NOT IN ('geprek','crispy','gangnam','makanan','minuman') THEN dp.jumlah ELSE 0 END) AS tambahan_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam','makanan') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS makanan_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS minuman_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) NOT IN ('geprek','crispy','gangnam','makanan','minuman') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS tambahan_profit
                FROM detail_pesanan dp
                JOIN menu m ON m.id_menu = dp.id_menu
                GROUP BY dp.id_pesanan
            ) oi ON oi.id_pesanan = p.id_pesanan
            WHERE p.payment_status = 'paid'
              AND {$whereClause}
            GROUP BY {$groupBy}
            ORDER BY {$orderBy}
        ", $bindings);

        $totalPendapatan = 0;
        $totalTransaksi = 0;
        $totalItem = 0;
        $totalMakanan = 0;
        $totalMinuman = 0;
        $totalTambahan = 0;
        $totalMakananProfit = 0;
        $totalMinumanProfit = 0;
        $totalTambahanProfit = 0;

        foreach ($rows as $r) {
            $totalPendapatan += (int) $r->total_pendapatan;
            $totalTransaksi += (int) $r->total_transaksi;
            $totalItem += (int) $r->total_item;
            $totalMakanan += (int) $r->makanan_qty;
            $totalMinuman += (int) $r->minuman_qty;
            $totalTambahan += (int) $r->tambahan_qty;
            $totalMakananProfit += (int) $r->makanan_profit;
            $totalMinumanProfit += (int) $r->minuman_profit;
            $totalTambahanProfit += (int) $r->tambahan_profit;
        }

        $admin = Auth::user()->nama_user ?? 'Administrator';

        if ($filterType === 'weekly') {
            $from = "Pekan " . str_replace('-W', ' Tahun ', request('week', now()->format('Y-\Ww')));
            $to = "Pekan " . str_replace('-W', ' Tahun ', request('week', now()->format('Y-\Ww')));
        } elseif ($filterType === 'monthly') {
            $from = "1 " . \Carbon\Carbon::parse($selectedMonth . '-01')->translatedFormat('F Y');
            $to = \Carbon\Carbon::parse($selectedMonth . '-01')->endOfMonth()->translatedFormat('d F Y');
        } elseif ($filterType === 'yearly') {
            $from = "1 Januari " . $selectedYear;
            $to = "31 Desember " . $selectedYear;
        }

        $transactions = DB::select("
            SELECT 
                p.id_pesanan,
                p.kode_pesanan,
                p.paid_at,
                p.total_harga,
                p.payment_method,
                u.nama_user AS nama_pelanggan,
                oi.total_item,
                oi.item_details,
                (oi.makanan_profit + oi.minuman_profit + oi.tambahan_profit) AS total_profit
            FROM pesanan p
            LEFT JOIN users u ON u.id_user = p.id_user
            LEFT JOIN (
                SELECT 
                    dp.id_pesanan,
                    SUM(dp.jumlah) AS total_item,
                    STRING_AGG(m.nama || ' (' || dp.jumlah || 'x)', ', ') AS item_details,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam','makanan') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS makanan_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS minuman_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) NOT IN ('geprek','crispy','gangnam','makanan','minuman') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS tambahan_profit
                FROM detail_pesanan dp
                JOIN menu m ON m.id_menu = dp.id_menu
                GROUP BY dp.id_pesanan
            ) oi ON oi.id_pesanan = p.id_pesanan
            WHERE p.payment_status = 'paid'
              AND {$whereClause}
            ORDER BY p.paid_at DESC
        ", $bindings);

        $html = view('admin.laporan.export', compact(
            'rows',
            'from',
            'to',
            'filterType',
            'totalPendapatan',
            'totalTransaksi',
            'totalItem',
            'totalMakanan',
            'totalMinuman',
            'totalTambahan',
            'totalMakananProfit',
            'totalMinumanProfit',
            'totalTambahanProfit',
            'admin',
            'transactions'
        ))->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "laporan-{$from}-{$to}.pdf";

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
