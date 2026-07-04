<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
    ];
    /**
     * Semua menu yang termasuk dalam kategori ini.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Hitung jumlah menu tersedia dalam kategori (untuk badge "Makanan (10)").
     */
    public function getMenuCountAttribute(): int
    {
        return $this->menus()->count();
    }
}
