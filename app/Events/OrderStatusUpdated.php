<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event ini di-broadcast tiap kali status order berubah
 * (diterima -> dimasak -> disajikan), agar halaman tracking
 * customer ter-update otomatis tanpa refresh (via Reverb/WebSocket).
 */
class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Channel publik per-order, di-listen oleh halaman tracking customer.
     * Contoh nama channel: order.15
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('order.' . $this->order->id),
            new PrivateChannel('kitchen-dashboard'), // channel privat untuk dashboard admin/operator
        ];
    }

    /**
     * Nama event yang didengarkan oleh frontend (Echo.listen).
     */
    public function broadcastAs(): string
    {
        return 'status.updated';
    }

    /**
     * Data yang dikirim ke frontend saat event terjadi.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'queue_number' => $this->order->queue_number,
            'status' => $this->order->status,
            'status_label' => $this->order->status_label,
            'status_step' => $this->order->status_step,
            'estimated_minutes' => $this->order->estimated_minutes,
            'customer_name' => $this->order->customer_name,
            'table_label' => $this->order->table?->label,
            'total_amount' => $this->order->total_amount,
            'formatted_total' => $this->order->formatted_total,
        ];
    }
}
