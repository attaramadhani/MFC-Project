<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todayRevenue = (int) DB::table('pesanan')
            ->where('payment_status', 'paid')
            ->whereDate('paid_at', today())
            ->sum('total_harga');

        $monthRevenue = (int) DB::table('pesanan')
            ->where('payment_status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('total_harga');

        $totalTransaksiPaid = (int) DB::table('pesanan')
            ->where('payment_status', 'paid')
            ->count();

        $totalPelanggan = (int) DB::table('users')
            ->where('role', 'pelanggan')
            ->count();

        $totalMenu = (int) DB::table('menu')->count();

        $statusCount = [
            'created' => 0,
            'processing' => 0,
            'ready' => 0,
            'completed' => 0,
            'canceled' => 0,
        ];

        $statusRows = DB::table('pesanan')
            ->select('order_status', DB::raw('COUNT(*) as jml'))
            ->groupBy('order_status')
            ->get();

        foreach ($statusRows as $row) {
            if (array_key_exists($row->order_status, $statusCount)) {
                $statusCount[$row->order_status] = (int) $row->jml;
            }
        }

        $rowsDaily = DB::table('pesanan')
            ->selectRaw('CAST(paid_at AS DATE) as tgl, COALESCE(SUM(total_harga),0) as total')
            ->where('payment_status', 'paid')
            ->whereDate('paid_at', '>=', now()->subDays(29)->toDateString())
            ->whereDate('paid_at', '<=', now()->toDateString())
            ->groupBy(DB::raw('CAST(paid_at AS DATE)'))
            ->orderBy('tgl')
            ->get();

        $mapDaily = [];
        foreach ($rowsDaily as $row) {
            $mapDaily[$row->tgl] = (int) $row->total;
        }

        $dailyLabels = [];
        $dailyValues = [];

        for ($i = 29; $i >= 0; $i--) {
            $tgl = now()->subDays($i);
            $key = $tgl->toDateString();

            $dailyLabels[] = $tgl->format('d M');
            $dailyValues[] = $mapDaily[$key] ?? 0;
        }

        $isPgsql = (DB::connection()->getDriverName() === 'pgsql');
        $formatExpr = $isPgsql ? "TO_CHAR(paid_at, 'YYYY-MM')" : "DATE_FORMAT(paid_at, '%Y-%m')";

        $rowsMonthly = DB::table('pesanan')
            ->selectRaw("$formatExpr as ym, COALESCE(SUM(total_harga),0) as total")
            ->where('payment_status', 'paid')
            ->where('paid_at', '>=', now()->startOfMonth()->subMonths(5))
            ->groupBy(DB::raw($formatExpr))
            ->orderBy('ym')
            ->get();

        $mapMonthly = [];
        foreach ($rowsMonthly as $row) {
            $mapMonthly[$row->ym] = (int) $row->total;
        }

        $monthLabels = [];
        $monthValues = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->startOfMonth()->subMonths($i);
            $ym = $date->format('Y-m');

            $monthLabels[] = $date->format('M Y');
            $monthValues[] = $mapMonthly[$ym] ?? 0;
        }

        $adminName = Auth::user()->nama_user ?? 'Admin';
        $adminInitial = mb_strtoupper(mb_substr($adminName, 0, 1));

        return view('admin.dashboard', compact(
            'todayRevenue',
            'monthRevenue',
            'totalTransaksiPaid',
            'totalPelanggan',
            'totalMenu',
            'statusCount',
            'dailyLabels',
            'dailyValues',
            'monthLabels',
            'monthValues',
            'adminName',
            'adminInitial'
        ));
    }
}