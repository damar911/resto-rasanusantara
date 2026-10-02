<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Tanpa login, semua orang boleh membuat order (self-order dari meja).
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:100'],
            'table_id' => ['required', 'exists:restaurant_tables,id'],
            'whatsapp_number' => ['required', 'string', 'regex:/^[0-9+]{9,15}$/'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama pemesan wajib diisi.',
            'table_id.required' => 'Silakan pilih nomor meja.',
            'table_id.exists' => 'Meja yang dipilih tidak valid.',
            'whatsapp_number.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp_number.regex' => 'Format nomor WhatsApp tidak valid.',
            'items.required' => 'Keranjang masih kosong, silakan pilih menu terlebih dahulu.',
            'items.min' => 'Keranjang masih kosong, silakan pilih menu terlebih dahulu.',
            'items.*.menu_id.exists' => 'Salah satu menu di keranjang tidak valid.',
            'items.*.quantity.min' => 'Jumlah pesanan minimal 1.',
        ];
    }
}
