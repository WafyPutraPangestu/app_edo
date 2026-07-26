<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

/**
 * Wrapper Midtrans yang SENGAJA tidak memakai notification callback/webhook.
 *
 * Sumber kebenaran status pembayaran didapat dengan cara "menarik" (pull)
 * status langsung dari Midtrans lewat checkStatus(), yang dipanggil berkala
 * (polling) dari sisi client (Livewire wire:poll). Ini menghindari kebutuhan
 * expose endpoint publik untuk notification_url — cocok untuk environment
 * skripsi/local yang tidak selalu punya domain publik.
 */
class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * Ambil snap token untuk transaksi. Kalau sudah pernah dibuat & masih
     * berupa string valid, dipakai ulang supaya tidak membuat token baru
     * setiap kali halaman pembayaran dibuka.
     */
    public function getSnapToken(Transaction $transaction): string
    {
        if (!empty($transaction->snap_token)) {
            return $transaction->snap_token;
        }

        $transaction->loadMissing(['user', 'releaseRequest.requestCharges.localCharge']);

        $itemDetails = $transaction->releaseRequest->requestCharges
            ->map(function ($charge) {
                return [
                    'id'       => 'charge-' . $charge->id,
                    'price'    => (int) round($charge->amount),
                    'quantity' => 1,
                    'name'     => \Illuminate\Support\Str::limit($charge->localCharge->name, 50, ''),
                ];
            })
            ->toArray();

        $params = [
            'transaction_details' => [
                'order_id'     => $transaction->invoice_number,
                'gross_amount' => (int) round($transaction->total_amount),
            ],
            'customer_details' => [
                'first_name' => $transaction->user->name,
                'email'      => $transaction->user->email,
                'phone'      => $transaction->user->phone ?: '08000000000',
            ],
            'item_details' => $itemDetails,
        ];

        $snapToken = Snap::getSnapToken($params);

        $transaction->update(['snap_token' => $snapToken]);

        return $snapToken;
    }

    /**
     * Query status transaksi LANGSUNG ke Midtrans (dipanggil oleh polling).
     * Return null kalau gagal / order belum ada di sisi Midtrans (misal
     * user belum sempat membuka Snap sama sekali).
     */
    public function checkStatus(string $orderId): ?object
    {
        try {
            return MidtransTransaction::status($orderId);
        } catch (\Exception $e) {
            Log::warning("Midtrans checkStatus gagal untuk order {$orderId}: " . $e->getMessage());

            return null;
        }
    }

    /**
     * Mapping transaction_status + fraud_status dari Midtrans ke status
     * internal aplikasi: unpaid | paid | failed.
     */
    public function mapStatus(object $result): string
    {
        $status = $result->transaction_status ?? null;
        $fraud  = $result->fraud_status ?? null;

        return match (true) {
            $status === 'capture' && $fraud === 'accept'          => 'paid',
            $status === 'capture' && $fraud === 'challenge'       => 'unpaid',
            $status === 'settlement'                              => 'paid',
            in_array($status, ['cancel', 'deny', 'expire'], true)  => 'failed',
            $status === 'pending'                                 => 'unpaid',
            default                                                => 'unpaid',
        };
    }
}
