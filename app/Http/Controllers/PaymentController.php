<?php

namespace App\Http\Controllers;

use App\Events\NewOrderCreated;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\RestaurantTable;
use App\Services\MidtransService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct(
        protected MidtransService $midtrans,
        protected WhatsAppService $whatsapp,
    ) {
    }

    /**
     * Webhook dari Midtrans (Payment Notification URL).
     * Data diverifikasi ulang ke API Midtrans lewat class Notification.
     */
    public function notification(): JsonResponse
    {
        try {
            $data = (array) (new Notification())->getResponse();
        } catch (\Throwable $e) {
            Log::warning('Notifikasi Midtrans tidak valid: ' . $e->getMessage());

            return response()->json(['message' => 'Notifikasi tidak valid.'], 400);
        }

        $order = Order::where('midtrans_order_id', $data['order_id'] ?? '')->first();

        if (! $order) {
            return response()->json(['message' => 'Order tidak ditemukan.'], 404);
        }

        $this->applyStatus($order, $data);

        return response()->json(['message' => 'OK']);
    }

    /**
     * Sinkronisasi manual dari frontend setelah popup Snap selesai.
     * Berguna saat webhook belum bisa masuk (misalnya di localhost tanpa ngrok).
     * Status tetap diambil dari Midtrans, bukan dari klien.
     */
    public function sync(Order $order): JsonResponse
    {
        if ($order->midtrans_order_id && $order->payment_status !== 'paid') {
            try {
                $this->applyStatus($order, $this->midtrans->getStatus($order->midtrans_order_id));
            } catch (\Throwable $e) {
                // Transaksi belum dibuat di Midtrans (popup ditutup sebelum memilih metode)
                Log::info('Sync status Midtrans: ' . $e->getMessage());
            }
        }

        return response()->json([
            'payment_status' => $order->fresh()->payment_status,
        ]);
    }

    /**
     * Terapkan status transaksi Midtrans ke order.
     */
    protected function applyStatus(Order $order, array $data): void
    {
        // Pastikan nominal cocok dengan total order
        if (isset($data['gross_amount']) && (int) round((float) $data['gross_amount']) !== (int) $order->total_amount) {
            Log::warning("Nominal Midtrans tidak cocok untuk order #{$order->id}.");

            return;
        }

        $status = $data['transaction_status'] ?? null;
        $fraud = $data['fraud_status'] ?? 'accept';
        $paymentType = $data['payment_type'] ?? null;

        if ($status === 'settlement' || ($status === 'capture' && $fraud === 'accept')) {
            $this->markAsPaid($order, $paymentType);
        } elseif (in_array($status, ['deny', 'cancel', 'expire', 'failure'], true)) {
            $this->markAsFailed($order, $status === 'expire' ? 'expired' : 'failed');
        } elseif ($paymentType && $order->payment_status === 'pending') {
            $order->update(['payment_type' => $paymentType]);
        }
    }

    protected function markAsPaid(Order $order, ?string $paymentType): void
    {
        // Idempotent: webhook & sync bisa datang bersamaan, notifikasi hanya sekali
        $justPaid = DB::transaction(function () use ($order, $paymentType) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();

            if ($locked->payment_status === 'paid') {
                return false;
            }

            $locked->update([
                'payment_status' => 'paid',
                'payment_type' => $paymentType,
                'paid_at' => now(),
            ]);

            // Meja otomatis terisi setelah pembayaran berhasil
            RestaurantTable::whereKey($locked->table_id)->first()
                ?->occupy($locked->customer_name, $locked->whatsapp_number);

            return true;
        });

        if (! $justPaid) {
            return;
        }

        $order = $order->fresh(['items', 'table']);

        // Baru sekarang pesanan muncul di dapur & pelanggan dapat notifikasi WA
        broadcast(new NewOrderCreated($order))->toOthers();

        $waResult = $this->whatsapp->sendOrderReceived($order);

        OrderStatusLog::where('order_id', $order->id)
            ->latest()
            ->first()
            ?->update([
                'whatsapp_sent' => $waResult['success'],
                'whatsapp_response' => $waResult['response'],
            ]);
    }

    protected function markAsFailed(Order $order, string $paymentStatus): void
    {
        if ($order->payment_status === 'paid') {
            return;
        }

        $order->update([
            'payment_status' => $paymentStatus,
            'status' => 'dibatalkan',
        ]);
    }
}
