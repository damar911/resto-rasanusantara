<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'number',
        'is_active',
        'is_occupied',
        'occupied_at',
        'occupant_name',
        'occupant_whatsapp',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_occupied' => 'boolean',
            'occupied_at' => 'datetime',
        ];
    }

    /**
     * Semua order yang pernah dibuat dari meja ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    /**
     * Scope hanya meja yang aktif/bisa dipakai.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Samakan format nomor WA (0812..., +62812..., 62812... dianggap sama).
     */
    public static function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        }

        return $digits;
    }

    /**
     * Meja bisa dipesan jika aktif dan kosong. Meja yang terisi tetap bisa
     * dipakai oleh pelanggan yang sama (nomor WA sama) untuk pesan tambahan.
     */
    public function isAvailableFor(?string $whatsapp = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (! $this->is_occupied) {
            return true;
        }

        $mine = self::normalizePhone($whatsapp);

        return $mine !== ''
            && $this->occupant_whatsapp !== null
            && $this->occupant_whatsapp === $mine;
    }

    public function occupy(?string $name = null, ?string $whatsapp = null): void
    {
        $this->update([
            'is_occupied' => true,
            'occupied_at' => now(),
            'occupant_name' => $name,
            'occupant_whatsapp' => $whatsapp ? self::normalizePhone($whatsapp) : null,
        ]);
    }

    public function free(): void
    {
        $this->update([
            'is_occupied' => false,
            'occupied_at' => null,
            'occupant_name' => null,
            'occupant_whatsapp' => null,
        ]);
    }
}
