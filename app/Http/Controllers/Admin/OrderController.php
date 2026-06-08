<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->get('status', '');
        $payFilter = $request->get('pay_status', '');
        $dateFrom = $request->get('from', '');
        $dateTo = $request->get('to', '');

        $query = DB::table('pesanan as p')
            ->join('users as u', 'u.id_user', '=', 'p.id_user')
            ->select('p.*', 'u.nama_user');

        if ($statusFilter !== '') {
            $query->where('p.order_status', $statusFilter);
        }

        if ($payFilter !== '') {
            $query->where('p.payment_status', $payFilter);
        }

        if ($dateFrom !== '') {
            $query->whereDate('p.created_at', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('p.created_at', '<=', $dateTo);
        }

        $orders = $query->orderByDesc('p.created_at')->limit(100)->get();

        return view('admin.pesanan.index', compact(
            'orders',
            'statusFilter',
            'payFilter',
            'dateFrom',
            'dateTo'
        ));
    }

    public function show($id)
    {
        $order = DB::table('pesanan as p')
            ->join('users as u', 'u.id_user', '=', 'p.id_user')
            ->where('p.id_pesanan', $id)
            ->select('p.*', 'u.nama_user')
            ->first();

        if (!$order) {
            return response('<div class="text-center text-muted small py-3">Pesanan tidak ditemukan.</div>', 404);
        }

        $items = DB::table('detail_pesanan as dp')
            ->join('menu as m', 'm.id_menu', '=', 'dp.id_menu')
            ->leftJoin('menu as mp', 'mp.id_menu', '=', 'dp.id_menu_paket')
            ->where('dp.id_pesanan', $id)
            ->orderBy('dp.id_menu_paket') // Group pakets together visually if needed
            ->orderBy('m.nama')
            ->get(['dp.*', 'm.nama', 'mp.nama as nama_paket']);

        $pay = DB::table('pembayaran')
            ->where('id_pesanan', $id)
            ->orderByDesc('id_pembayaran')
            ->first();

        $logs = DB::table('riwayat_status_pesanan as r')
            ->leftJoin('users as u', 'u.id_user', '=', 'r.diubah_oleh')
            ->where('r.id_pesanan', $id)
            ->orderBy('r.dibuat_pada')
            ->get(['r.*', 'u.nama_user']);

        return view('admin.pesanan.detail', compact('order', 'items', 'pay', 'logs'));
    }

    public function updateStatus(Request $request, $id)
    {
        $newStatus = $request->input('order_status', '');

        $order = DB::table('pesanan')->where('id_pesanan', $id)->first();

        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        $currentStatus = $order->order_status;
        $paymentStatus = $order->payment_status;
        $paymentMethod = $order->payment_method ?? 'midtrans';

        $allowedTransitions = [
            'created' => ['processing', 'canceled'],
            'waiting_confirmation' => ['processing', 'canceled'],
            'processing' => ['ready', 'canceled'],
            'ready' => ['completed', 'canceled'],
            'completed' => [],
            'canceled' => [],
        ];

        $can = isset($allowedTransitions[$currentStatus]) &&
            in_array($newStatus, $allowedTransitions[$currentStatus], true);

        $needPaid = in_array($newStatus, ['processing', 'ready', 'completed'], true);

        if ($needPaid && $paymentMethod !== 'cash' && $paymentStatus !== 'paid') {
            return back()->with('error', 'Pesanan belum dibayar, tidak bisa diproses.');
        }

        if (!$can) {
            return back()->with('error', 'Transisi status tidak diizinkan.');
        }

        DB::beginTransaction();

        try {
            $update = ['order_status' => $newStatus];

            switch ($newStatus) {
                case 'processing':
                    $update['processed_at'] = now();
                    break;
                case 'ready':
                    $update['ready_at'] = now();
                    break;
                case 'completed':
                    $update['completed_at'] = now();
                    break;
                case 'canceled':
                    $update['canceled_at'] = now();
                    if ($order->stok_dikurangi) {
                        $items = DB::table('detail_pesanan')->where('id_pesanan', $id)->get();
                        foreach ($items as $item) {
                            DB::table('menu')
                                ->where('id_menu', $item->id_menu)
                                ->increment('stok', $item->jumlah);
                        }
                        $update['stok_dikurangi'] = DB::raw('FALSE');
                    }
                    break;
            }

            DB::table('pesanan')->where('id_pesanan', $id)->update($update);

            if ($newStatus === 'completed' && $paymentMethod === 'cash' && $paymentStatus !== 'paid') {
                DB::table('pesanan')
                    ->where('id_pesanan', $id)
                    ->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                    ]);

                DB::table('riwayat_status_pesanan')->insert([
                    'id_pesanan' => $id,
                    'tipe' => 'payment',
                    'status_lama' => $paymentStatus,
                    'status_baru' => 'paid',
                    'diubah_oleh' => Auth::id(),
                    'keterangan' => 'COD diterima saat pesanan selesai.',
                ]);
            }

            DB::table('riwayat_status_pesanan')->insert([
                'id_pesanan' => $id,
                'tipe' => 'order',
                'status_lama' => $currentStatus,
                'status_baru' => $newStatus,
                'diubah_oleh' => Auth::id(),
                'keterangan' => 'Perubahan status oleh admin.',
            ]);

            DB::commit();

            return back()->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate status: ' . $e->getMessage());
        }
    }
}