<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function notification(Request $request)
    {
        $raw = $request->getContent();
        $data = json_decode($raw, true);

        Log::info('MIDTRANS NOTIFICATION MASUK', [
            'raw' => $raw,
            'data' => $data,
        ]);

        if (!$data || !is_array($data)) {
            return response('Invalid JSON', 400);
        }

        $order_id         = $data['order_id'] ?? '';
        $status_code      = $data['status_code'] ?? '';
        $gross_amount     = $data['gross_amount'] ?? '';
        $signature_key    = $data['signature_key'] ?? '';
        $tx_status        = $data['transaction_status'] ?? '';
        $fraud_status     = $data['fraud_status'] ?? null;
        $transaction_id   = $data['transaction_id'] ?? null;
        $settlement_time  = $data['settlement_time'] ?? null;
        $transaction_time = $data['transaction_time'] ?? null;

        if (
            $order_id === '' ||
            $status_code === '' ||
            $gross_amount === '' ||
            $signature_key === '' ||
            $tx_status === ''
        ) {
            Log::warning('MIDTRANS: field wajib kurang', $data);
            return response('Missing fields', 400);
        }

        // 1) VERIFIKASI SIGNATURE
        $serverKey = env('MIDTRANS_SERVER_KEY', '');
        $expected = hash('sha512', $order_id . $status_code . $gross_amount . $serverKey);

        if (!hash_equals($expected, $signature_key)) {
            Log::warning('MIDTRANS: signature tidak valid', [
                'order_id' => $order_id,
                'expected' => $expected,
                'received' => $signature_key,
            ]);
            return response('Invalid signature', 403);
        }

        // 2) CARI PEMBAYARAN BERDASARKAN provider_order_id
        $pembayaran = DB::table('pembayaran')
            ->where('provider', 'midtrans')
            ->where('provider_order_id', $order_id)
            ->orderByDesc('id_pembayaran')
            ->first();

        $id_pesanan = $pembayaran->id_pesanan ?? 0;
        $id_pembayaran = $pembayaran->id_pembayaran ?? 0;

        // fallback kalau record pembayaran belum ada
        if (!$id_pesanan) {
            $pesanan = DB::table('pesanan')
                ->where('kode_pesanan', $order_id)
                ->first();

            $id_pesanan = $pesanan->id_pesanan ?? 0;
        }

        if (!$id_pesanan) {
            Log::warning('MIDTRANS: order tidak ditemukan di DB', [
                'order_id' => $order_id,
            ]);
            return response('Order not found', 404);
        }

        $pesanan = DB::table('pesanan')
            ->where('id_pesanan', $id_pesanan)
            ->first();

        if (!$pesanan) {
            Log::warning('MIDTRANS: pesanan tidak ditemukan', [
                'id_pesanan' => $id_pesanan,
                'order_id' => $order_id,
            ]);
            return response('Pesanan not found', 404);
        }

        // 3) MAPPING STATUS MIDTRANS -> STATUS DB
        $newPesananStatus = 'pending';
        $newPembayaranStatus = 'pending';

        if ($tx_status === 'settlement') {
            $newPesananStatus = 'paid';
            $newPembayaranStatus = 'paid';
        } elseif ($tx_status === 'capture') {
            if ($fraud_status === null || $fraud_status === '' || $fraud_status === 'accept') {
                $newPesananStatus = 'paid';
                $newPembayaranStatus = 'paid';
            } else {
                $newPesananStatus = 'pending';
                $newPembayaranStatus = 'pending';
            }
        } elseif ($tx_status === 'pending') {
            $newPesananStatus = 'pending';
            $newPembayaranStatus = 'pending';
        } elseif ($tx_status === 'expire') {
            $newPesananStatus = 'expired';
            $newPembayaranStatus = 'failed';
        } elseif ($tx_status === 'cancel' || $tx_status === 'deny' || $tx_status === 'failure') {
            $newPesananStatus = 'failed';
            $newPembayaranStatus = 'failed';
        } elseif ($tx_status === 'refund' || $tx_status === 'partial_refund') {
            $newPesananStatus = 'refunded';
            $newPembayaranStatus = 'refunded';
        }

        try {
            DB::beginTransaction();

            // 4) UPDATE PESANAN
            $updatePesanan = [
                'payment_status' => $newPesananStatus,
            ];

            if ($newPesananStatus === 'paid' && empty($pesanan->paid_at)) {
                $updatePesanan['paid_at'] = $settlement_time
                    ? date('Y-m-d H:i:s', strtotime($settlement_time))
                    : now();
            }

            if (in_array($newPesananStatus, ['expired', 'failed', 'refunded'], true) && $pesanan->stok_dikurangi) {
                $items = DB::table('detail_pesanan')->where('id_pesanan', $id_pesanan)->get();
                foreach ($items as $item) {
                    DB::table('menu')
                        ->where('id_menu', $item->id_menu)
                        ->increment('stok', $item->jumlah);
                }
                $updatePesanan['stok_dikurangi'] = 0;
            }

            DB::table('pesanan')
                ->where('id_pesanan', $id_pesanan)
                ->update($updatePesanan);

            // 5) UPDATE PEMBAYARAN TERAKHIR
            if ($id_pembayaran) {
                DB::table('pembayaran')
                    ->where('id_pembayaran', $id_pembayaran)
                    ->update([
                        'status' => $newPembayaranStatus,
                        'settlement_time' => $newPembayaranStatus === 'paid'
                            ? ($settlement_time
                                ? date('Y-m-d H:i:s', strtotime($settlement_time))
                                : now())
                            : $pembayaran->settlement_time,
                        'provider_transaction_id' => $transaction_id ?: $pembayaran->provider_transaction_id,
                        'raw_response' => json_encode($data),
                    ]);
            }

            // 6) SIMPAN RIWAYAT STATUS PAYMENT
            if (($pesanan->payment_status ?? null) !== $newPesananStatus) {
                DB::table('riwayat_status_pesanan')->insert([
                    'id_pesanan' => $id_pesanan,
                    'tipe' => 'payment',
                    'status_lama' => $pesanan->payment_status,
                    'status_baru' => $newPesananStatus,
                    'diubah_oleh' => null,
                    'keterangan' => 'Update status pembayaran dari Midtrans.',
                    'dibuat_pada' => now(),
                ]);
            }

            DB::commit();

            Log::info('MIDTRANS: callback berhasil diproses', [
                'id_pesanan' => $id_pesanan,
                'id_pembayaran' => $id_pembayaran,
                'order_id' => $order_id,
                'transaction_status' => $tx_status,
                'payment_status' => $newPesananStatus,
                'payment_record_status' => $newPembayaranStatus,
            ]);

            return response('OK', 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('MIDTRANS: gagal update callback', [
                'order_id' => $order_id,
                'error' => $e->getMessage(),
            ]);

            return response('DB error: ' . $e->getMessage(), 500);
        }
    }
}