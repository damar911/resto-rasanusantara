<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'photo',
        'availability',
    ];

    /**
     * Kategori dari menu ini (Makanan, Minuman, Cemilan).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Cek apakah menu masih bisa dipesan.
     */
    public function isAvailable(): bool
    {
        return $this->availability === 'available';
    }

    /**
     * URL publik foto menu. Jika tidak ada foto, kembalikan placeholder.
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return Storage::url($this->photo);
        }

        return asset('images/menu-placeholder.svg');
    }

    /**
     * Harga terformat dalam Rupiah, contoh: Rp 25.000
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Scope hanya menu yang tersedia (belum habis).
     */
    public function scopeAvailable($query)
    {
        return $query->where('availability', 'available');
    }
}
