<?php
namespace App\Http\Controllers\pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        $id_user = Auth::id();

        $items = DB::table('keranjang as k')
            ->join('menu as m', 'm.id_menu', '=', 'k.id_menu')
            ->where('k.id_user', $id_user)
            ->get([
                'k.id_menu',
                'k.jumlah',
                'm.nama',
                'm.harga'
            ]);

        $total = 0;
        foreach ($items as $item) {
            $total += $item->jumlah * $item->harga;
        }

        return view('pelanggan.checkout', [
            'items' => $items,
            'total' => $total
        ]);
    }

    public function process(Request $request)
    {
        $id_user = Auth::id();

        $id_pesanan_existing = (int) $request->input('id_pesanan', 0);
        $alamat_pengiriman = trim($request->input('alamat_pengiriman', ''));
        $wilayah_pengiriman = trim($request->input('wilayah_pengiriman', ''));
        $payment_method = $request->input('payment_method', 'midtrans');

        if (!in_array($payment_method, ['midtrans', 'cash'], true)) {
            $payment_method = 'midtrans';
        }

        $user = DB::table('users')
            ->where('id_user', $id_user)
            ->first();

        $nama_pelanggan = $user->nama_user ?? 'Pelanggan';

        try {
            DB::beginTransaction();

            $item_details = [];
            $subtotal_items = 0;
            $ongkir = 0;
            $grand_total = 0;

            $kode_pesanan = '';
            $id_pesanan = 0;
            $oldPaymentStatus = 'unpaid';
            $order_id = '';

            if ($wilayah_pengiriman !== '') {
                $ongkir = (int) (DB::table('wilayah_ongkir')
                    ->where('nama_wilayah', $wilayah_pengiriman)
                    ->value('ongkir') ?? 0);
            }

            if ($ongkir < 0) {
                $ongkir = 0;
            }

            // LANJUTKAN PEMBAYARAN PESANAN YANG SUDAH ADA
            
            if ($id_pesanan_existing > 0) {
                $pesanan = DB::table('pesanan')
                    ->where('id_pesanan', $id_pesanan_existing)
                    ->where('id_user', $id_user)
                    ->first();

                if (!$pesanan) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Pesanan tidak ditemukan.'
                    ]);
                }

                if (($pesanan->payment_status ?? '') === 'paid') {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Pesanan ini sudah dibayar.'
                    ]);
                }

                if (($pesanan->order_status ?? '') === 'canceled') {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Pesanan dibatalkan, tidak bisa dibayar.'
                    ]);
                }

                $id_pesanan = (int) $pesanan->id_pesanan;
                $kode_pesanan = $pesanan->kode_pesanan;
                $oldPaymentStatus = $pesanan->payment_status ?? 'unpaid';
                $payment_method = $pesanan->payment_method ?? $payment_method;

                $items = DB::table('detail_pesanan as d')
                    ->join('menu as m', 'm.id_menu', '=', 'd.id_menu')
                    ->where('d.id_pesanan', $id_pesanan)
                    ->get([
                        'd.id_menu',
                        'd.jumlah',
                        'd.harga',
                        'm.nama',
                    ]);

                if ($items->isEmpty()) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Detail pesanan kosong.'
                    ]);
                }

                foreach ($items as $item) {
                    $qty = (int) $item->jumlah;
                    $harga = (int) $item->harga;
                    $subtotal = $qty * $harga;
                    $subtotal_items += $subtotal;

                    $item_details[] = [
                        'id' => $item->id_menu,
                        'price' => $harga,
                        'quantity' => $qty,
                        'name' => substr($item->nama, 0, 50),
                    ];
                }

                if ($ongkir > 0) {
                    $item_details[] = [
                        'id' => 'ONGKIR',
                        'price' => $ongkir,
                        'quantity' => 1,
                        'name' => 'Ongkos Kirim',
                    ];
                }

                $grand_total = $subtotal_items + $ongkir;

                DB::table('pesanan')
                    ->where('id_pesanan', $id_pesanan)
                    ->update([
                        'total_harga' => $grand_total,
                        'ongkir' => $ongkir,
                        'alamat_pengiriman' => $alamat_pengiriman ?: ($pesanan->alamat_pengiriman ?? null),
                        'wilayah_pengiriman' => $wilayah_pengiriman ?: ($pesanan->wilayah_pengiriman ?? null),
                        'payment_method' => $payment_method,
                    ]);

                $order_id = $kode_pesanan . '-R' . time();
            }

            // CHECKOUT BARU DARI KERANJANG

            else {
                if ($alamat_pengiriman === '') {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Alamat pengantaran wajib diisi.'
                    ]);
                }

                if ($wilayah_pengiriman === '') {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Silakan pilih wilayah.'
                    ]);
                }

                $items = DB::table('keranjang as k')
                    ->join('menu as m', 'm.id_menu', '=', 'k.id_menu')
                    ->where('k.id_user', $id_user)
                    ->get([
                        'k.id_menu',
                        'k.jumlah',
                        'm.nama',
                        'm.harga'
                    ]);

                if ($items->isEmpty()) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Keranjang kosong'
                    ]);
                }

                foreach ($items as $item) {
                    $qty = (int) $item->jumlah;
                    $harga = (int) $item->harga;
                    $subtotal = $qty * $harga;
                    $subtotal_items += $subtotal;

                    $item_details[] = [
                        'id' => $item->id_menu,
                        'price' => $harga,
                        'quantity' => $qty,
                        'name' => substr($item->nama, 0, 50),
                    ];
                }

                if ($ongkir > 0) {
                    $item_details[] = [
                        'id' => 'ONGKIR',
                        'price' => $ongkir,
                        'quantity' => 1,
                        'name' => 'Ongkos Kirim',
                    ];
                }

                $grand_total = $subtotal_items + $ongkir;

                $kode_pesanan = 'ORD-' . date('YmdHis') . '-' . $id_user;
                $order_status_awal = ($payment_method === 'cash') ? 'waiting_confirmation' : 'created';

                $id_pesanan = DB::table('pesanan')->insertGetId([
                    'id_user' => $id_user,
                    'kode_pesanan' => $kode_pesanan,
                    'total_harga' => $grand_total,
                    'alamat_pengiriman' => $alamat_pengiriman,
                    'wilayah_pengiriman' => $wilayah_pengiriman,
                    'ongkir' => $ongkir,
                    'payment_method' => $payment_method,
                    'payment_status' => 'unpaid',
                    'order_status' => $order_status_awal,
                    'created_at' => now(),
                ]);

                foreach ($items as $item) {
                    DB::table('detail_pesanan')->insert([
                        'id_pesanan' => $id_pesanan,
                        'id_menu' => $item->id_menu,
                        'jumlah' => $item->jumlah,
                        'harga' => $item->harga,
                    ]);
                }

                DB::table('keranjang')
                    ->where('id_user', $id_user)
                    ->delete();

                $order_id = $kode_pesanan;
            }

            // JALUR CASH / COD

            if ($payment_method === 'cash') {
                DB::table('pesanan')
                    ->where('id_pesanan', $id_pesanan)
                    ->update([
                        'order_status' => 'waiting_confirmation',
                        'payment_status' => 'unpaid',
                    ]);

                DB::table('pembayaran')->insert([
                    'id_pesanan' => $id_pesanan,
                    'provider' => 'cash',
                    'metode' => 'cod',
                    'gross_amount' => $grand_total,
                    'status' => 'pending',
                    'transaction_time' => now(),
                    'settlement_time' => null,
                    'provider_order_id' => null,
                    'provider_transaction_id' => null,
                    'raw_response' => null,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'mode' => 'cash',
                    'message' => 'Pesanan COD berhasil dibuat.',
                    'kode_pesanan' => $kode_pesanan,
                    'id_pesanan' => $id_pesanan,
                    'ongkir' => $ongkir,
                    'total' => $grand_total,
                ]);
            }

            // JALUR MIDTRANS

            $payload = [
                'transaction_details' => [
                    'order_id' => $order_id,
                    'gross_amount' => (int) $grand_total,
                ],
                'customer_details' => [
                    'first_name' => $nama_pelanggan,
                ],
                'item_details' => $item_details,
            ];

            $midtransApiUrl = env('MIDTRANS_IS_PRODUCTION')
                ? 'https://app.midtrans.com/snap/v1/transactions'
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            $serverKey = env('MIDTRANS_SERVER_KEY');

            $ch = curl_init($midtransApiUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Basic ' . base64_encode($serverKey . ':'),
                ],
                CURLOPT_POSTFIELDS => json_encode($payload),
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($response === false) {
                $error = curl_error($ch);
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Error cURL: ' . $error
                ]);
            }

            $data = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300 && isset($data['token'])) {
                DB::table('pesanan')
                    ->where('id_pesanan', $id_pesanan)
                    ->update([
                        'payment_status' => 'pending',
                    ]);

                DB::table('pembayaran')->insert([
                    'id_pesanan' => $id_pesanan,
                    'provider' => 'midtrans',
                    'metode' => 'qris',
                    'gross_amount' => $grand_total,
                    'status' => 'pending',
                    'transaction_time' => now(),
                    'settlement_time' => null,
                    'provider_order_id' => $order_id,
                    'provider_transaction_id' => $data['token'] ?? null,
                    'raw_response' => json_encode($data),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'token' => $data['token'],
                    'redirect_url' => $data['redirect_url'] ?? null,
                    'kode_pesanan' => $kode_pesanan,
                    'id_pesanan' => $id_pesanan,
                    'ongkir' => $ongkir,
                    'total' => $grand_total,
                ]);
            }

            Log::info('Midtrans response', [
                'http_code' => $httpCode,
                'response' => $data,
                'payload' => $payload,
            ]);

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi Midtrans',
                'midtrans_response' => $data,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}