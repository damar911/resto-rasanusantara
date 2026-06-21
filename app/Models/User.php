<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cek apakah user adalah Admin Dapur (akses penuh: menu, kategori, laporan, antrean).
     */
    public function isAdminDapur(): bool
    {
        return $this->role === 'admin_dapur';
    }

    /**
     * Cek apakah user adalah Operator (hanya kelola antrean).
     */
    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Relasi ke log perubahan status order yang dilakukan user ini.
     */
    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class, 'changed_by');
    }
}
