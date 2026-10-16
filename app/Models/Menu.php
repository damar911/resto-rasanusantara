<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'promo_price',
        'promo_starts_at',
        'promo_ends_at',
        'photo',
        'availability',
        'avg_rating',
        'rating_count',
    ];

    protected function casts(): array
    {
        return [
            'promo_starts_at' => 'datetime',
            'promo_ends_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(MenuRating::class);
    }

    public function isAvailable(): bool
    {
        return $this->availability === 'available';
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return Storage::url($this->photo);
        }
        return asset('images/menu-placeholder.svg');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Status promo: 'active' (sedang berlaku), 'scheduled' (belum mulai),
     * 'expired' (sudah berakhir), atau null (tidak ada promo).
     */
    public function promoStatus(): ?string
    {
        if ($this->promo_price === null || $this->promo_price >= $this->price) {
            return null;
        }

        if ($this->promo_starts_at && $this->promo_starts_at->isFuture()) {
            return 'scheduled';
        }

        if ($this->promo_ends_at && $this->promo_ends_at->isPast()) {
            return 'expired';
        }

        return 'active';
    }

    public function isOnPromo(): bool
    {
        return $this->promoStatus() === 'active';
    }

    /**
     * Harga yang dipakai untuk pemesanan (harga promo jika promo sedang berlaku).
     */
    public function getEffectivePriceAttribute(): int
    {
        return (int) ($this->isOnPromo() ? $this->promo_price : $this->price);
    }

    public function getFormattedEffectivePriceAttribute(): string
    {
        return 'Rp ' . number_format($this->effective_price, 0, ',', '.');
    }

    public function getDiscountPercentAttribute(): int
    {
        if (! $this->isOnPromo() || $this->price <= 0) {
            return 0;
        }

        return (int) round((1 - $this->promo_price / $this->price) * 100);
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability', 'available');
    }

    /**
     * Recalculate dan simpan ulang avg_rating & rating_count setelah ada rating baru.
     */
    public function recalculateRating(): void
    {
        $this->avg_rating   = $this->ratings()->avg('rating') ?? 0;
        $this->rating_count = $this->ratings()->count();
        $this->save();
    }
}
