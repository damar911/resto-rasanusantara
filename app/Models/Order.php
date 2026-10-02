<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_number',
        'customer_name',
        'table_id',
        'whatsapp_number',
        'payment_method',
        'payment_status',
        'midtrans_order_id',
        'snap_token',
        'payment_type',
        'paid_at',
        'total_amount',
        'status',
        'estimated_minutes',
        'confirmed_at',
        'cooking_at',
        'served_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cooking_at' => 'datetime',
            'served_at' => 'datetime',
        ];
    }

    /**
     * Urutan tahapan status dapur, dipakai untuk progress bar di UI tracking.
     */
    public const STATUS_FLOW = ['diterima', 'dimasak', 'disajikan'];

    /**
     * Meja tempat order ini dipesan.
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    /**
     * Item-item menu dalam order ini.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Riwayat perubahan status order (untuk audit & WA log).
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->latest();
    }

    /**
     * Total harga terformat, contoh: Rp 85.000
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Label status dalam Bahasa Indonesia, dipakai untuk tampilan UI.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'diterima' => 'Diterima',
            'dimasak' => 'Dimasak',
            'disajikan' => 'Disajikan',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Index tahap status saat ini dalam STATUS_FLOW (untuk progress bar 0,1,2).
     * Return -1 jika dibatalkan.
     */
    public function getStatusStepAttribute(): int
    {
        return array_search($this->status, self::STATUS_FLOW, true) ?: 0;
    }

    /**
     * Scope order yang masih aktif diproses (belum disajikan/dibatalkan).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['diterima', 'dimasak']);
    }

    /**
     * Scope order yang sudah selesai hari ini (untuk laporan omset).
     */
    public function scopeCompletedToday($query)
    {
        return $query->where('status', 'disajikan')
            ->whereDate('created_at', today());
    }
}
