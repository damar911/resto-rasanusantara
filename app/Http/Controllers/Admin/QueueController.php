<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function __construct(protected WhatsAppService $whatsapp)
    {
    }

    /**
     * Halaman manajemen antrean dapur (Admin Dapur & Operator).
     */
    public function index(): View
{
    $orders = Order::with(['items', 'table'])
        ->whereDate('created_at', today())
        ->whereIn('payment_status', ['paid', 'confirmed'])
        ->orderBy('created_at')
        ->get()
        ->map(function ($o) {
            return [
                'id' => $o->id,
                'queue_number' => $o->queue_number,
                'customer_name' => $o->customer_name,
                'table_label' => $o->table->label,
                'status' => $o->status,
                'items_count' => $o->items->sum('quantity'),
                'items' => $o->items->map(fn ($i) => [
                    'name' => $i->menu_name,
                    'quantity' => $i->quantity,
                ])->values(),
                'formatted_total' => $o->formatted_total,
                'created_at' => $o->created_at->format('H:i'),
            ];
        });

    return view('admin.queue.index', [
        'orders' => $orders,
    ]);
}

    /**
     * Update status order ke tahap berikutnya (diterima -> dimasak -> disajikan)
     * atau dibatalkan. Broadcast perubahan + kirim notifikasi WA otomatis.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:diterima,dimasak,disajikan,dibatalkan'],
        ]);

        $newStatus = $validated['status'];

        $order->status = $newStatus;

        match ($newStatus) {
            'dimasak' => $order->cooking_at = now(),
            'disajikan' => $order->served_at = now(),
            default => null,
        };

        $order->save();

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $newStatus,
            'changed_by' => $request->user()->id,
        ]);

        // Broadcast real-time ke halaman tracking customer & dashboard admin lain
        broadcast(new OrderStatusUpdated($order))->toOthers();

        // Kirim notifikasi WhatsApp otomatis sesuai status baru
        $waResult = $this->whatsapp->sendStatusUpdate($order);

        OrderStatusLog::where('order_id', $order->id)
            ->where('status', $newStatus)
            ->latest()
            ->first()
            ?->update([
                'whatsapp_sent' => $waResult['success'],
                'whatsapp_response' => $waResult['response'],
            ]);

        return response()->json([
            'success' => true,
            'message' => "Status pesanan #{$order->queue_number} berhasil diubah menjadi \"{$order->status_label}\".",
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'status_label' => $order->status_label,
            ],
        ]);
    }
}
