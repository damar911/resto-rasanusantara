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
 * Event ini di-broadcast saat ada order baru masuk dari customer,
 * supaya dashboard admin/operator langsung menampilkan antrean baru
 * tanpa perlu refresh halaman.
 */
class NewOrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load('items', 'table');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('kitchen-dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'queue_number' => $this->order->queue_number,
            'customer_name' => $this->order->customer_name,
            'table_label' => $this->order->table?->label,
            'status' => $this->order->status,
            'total_amount' => $this->order->total_amount,
            'formatted_total' => $this->order->formatted_total,
            'items_count' => $this->order->items->sum('quantity'),
            'created_at' => $this->order->created_at->format('H:i'),
        ];
    }
}
