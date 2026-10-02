<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

/**
 * Service untuk integrasi Midtrans Snap.
 *
 * Setup .env:
 *   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxx
 *   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxx
 *   MIDTRANS_IS_PRODUCTION=false
 */
class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Buat Snap Token untuk sebuah order dan simpan ke database.
     */
    public function createSnapToken(Order $order): string
    {
        $order->loadMissing('items');

        // order_id Midtrans harus unik per transaksi
        $midtransOrderId = 'RN-' . $order->id . '-' . now()->timestamp;

        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => (int) $order->total_amount,
            ],
            // Total item_details harus sama dengan gross_amount
            'item_details' => $order->items->map(fn ($item) => [
                'id' => 'menu-' . $item->menu_id,
                'price' => (int) $item->menu_price,
                'quantity' => (int) $item->quantity,
                'name' => mb_substr($item->menu_name, 0, 50),
            ])->values()->all(),
            'customer_details' => [
                'first_name' => $order->customer_name,
                'phone' => $order->whatsapp_number,
            ],
            'expiry' => [
                'unit' => 'minutes',
                'duration' => 60,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $order->update([
            'midtrans_order_id' => $midtransOrderId,
            'snap_token' => $snapToken,
        ]);

        return $snapToken;
    }

    /**
     * Ambil status transaksi langsung dari Midtrans (sumber kebenaran).
     */
    public function getStatus(string $midtransOrderId): array
    {
        return (array) Transaction::status($midtransOrderId);
    }
}
