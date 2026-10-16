<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya Admin Dapur yang boleh kelola menu (dicek juga di middleware route)
        return $this->user()?->isAdminDapur() ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'integer', 'min:0'],
            'promo_price' => ['nullable', 'integer', 'min:0', 'lt:price'],
            'promo_starts_at' => ['nullable', 'date'],
            'promo_ends_at' => array_filter([
                'nullable',
                'date',
                $this->filled('promo_starts_at') ? 'after:promo_starts_at' : null,
            ]),
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'availability' => ['required', 'in:available,sold_out'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori menu wajib dipilih.',
            'name.required' => 'Nama menu wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.min' => 'Harga tidak boleh negatif.',
            'promo_price.lt' => 'Harga promo harus lebih murah dari harga normal.',
            'promo_ends_at.after' => 'Tanggal berakhir promo harus setelah tanggal mulai.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
