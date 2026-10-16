<?php

namespace App\Http\Controllers;

use App\Events\NewOrderCreated;
use App\Events\OrderStatusUpdated;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\RestaurantTable;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected MidtransService $midtrans)
    {
    }

    /**
     * Simpan order baru dari customer (tanpa login), hitung total,
     * generate nomor antrean, lalu minta Snap Token ke Midtrans.
     * Broadcast ke dapur & notifikasi WA baru dikirim setelah pembayaran
     * terkonfirmasi (lihat PaymentController).
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $order = DB::transaction(function () use ($validated) {
            // Kunci baris meja agar dua pelanggan tidak bisa memesan meja yang sama bersamaan
            $table = RestaurantTable::whereKey($validated['table_id'])->lockForUpdate()->first();

            if (! $table || ! $table->isAvailableFor($validated['whatsapp_number'])) {
                abort(422, 'Meja yang dipilih sedang terisi. Silakan pilih meja lain.');
            }

            $menus = Menu::whereIn('id', collect($validated['items'])->pluck('menu_id'))
                ->get()
                ->keyBy('id');

            $totalAmount = 0;
            $itemsToCreate = [];

            foreach ($validated['items'] as $item) {
                $menu = $menus->get($item['menu_id']);

                if (! $menu || ! $menu->isAvailable()) {
                    abort(422, "Menu \"{$menu?->name}\" sedang tidak tersedia.");
                }

                $subtotal = $menu->effective_price * $item['quantity'];
                $totalAmount += $subtotal;

                $itemsToCreate[] = [
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'menu_price' => $menu->effective_price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::create([
                'queue_number' => $this->generateQueueNumber(),
                'customer_name' => $validated['customer_name'],
                'table_id' => $validated['table_id'],
                'whatsapp_number' => $validated['whatsapp_number'],
                'payment_method' => 'midtrans',
                'payment_status' => 'pending',
                'total_amount' => $totalAmount,
                'status' => 'diterima',
                'confirmed_at' => now(),
            ]);

            foreach ($itemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'diterima',
            ]);

            return $order;
        });

        $order->load('items', 'table');

        // Minta Snap Token ke Midtrans
        try {
            $snapToken = $this->midtrans->createSnapToken($order);
        } catch (\Throwable $e) {
            Log::error('Gagal membuat Snap Token Midtrans: ' . $e->getMessage());

            $order->update([
                'payment_status' => 'failed',
                'status' => 'dibatalkan',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi sistem pembayaran. Silakan coba lagi.',
            ], 502);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesanan dibuat, silakan lanjutkan pembayaran.',
            'order' => [
                'id' => $order->id,
                'queue_number' => $order->queue_number,
                'snap_token' => $snapToken,
                'tracking_url' => route('order.track', $order->id),
            ],
        ], 201);
    }

    /**
     * Halaman tracking antrean real-time untuk customer (di-listen via Reverb).
     */
    public function track(Order $order): View
    {
        $order->load('items', 'table');

        return view('customer.track', [
            'order' => $order,
        ]);
    }

    /**
     * Endpoint AJAX untuk ambil status order terkini (fallback jika WebSocket gagal).
     */
    public function status(Order $order): JsonResponse
    {
        return response()->json([
            'order_id' => $order->id,
            'queue_number' => $order->queue_number,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'status_step' => $order->status_step,
            'estimated_minutes' => $order->estimated_minutes,
        ]);
    }

    /**
     * Generate nomor antrean harian, reset setiap hari mulai dari "01".
     * Format 2 digit: 01, 02, ..., 99, lalu lanjut 100, 101, dst jika lebih dari 99.
     */
    protected function generateQueueNumber(): string
    {
        $lastOrderToday = Order::whereDate('created_at', today())
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastOrderToday ? ((int) $lastOrderToday->queue_number + 1) : 1;

        return str_pad((string) $nextNumber, 2, '0', STR_PAD_LEFT);
    }

    public function trackSearch(): View
{
    return view('customer.track-search');
}
public function trackSearchSubmit(Request $request): RedirectResponse
{
    $request->validate([
        'queue_number' => ['required', 'string', 'max:10'],
    ]);

    $queueNumber = trim($request->input('queue_number'));

    $order = Order::whereDate('created_at', today())
        ->where('queue_number', $queueNumber)
        ->latest('id')
        ->first();

    if (! $order) {
        return back()
            ->withInput()
            ->withErrors([
                'queue_number' => 'Nomor antrean tidak ditemukan untuk hari ini. Pastikan nomor sesuai nota atau notifikasi WhatsApp Anda.',
            ]);
    }

    return redirect()->route('order.track', $order->id);
}
}
