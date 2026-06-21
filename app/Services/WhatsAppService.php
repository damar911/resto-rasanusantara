<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk mengirim notifikasi WhatsApp ke pelanggan
 * menggunakan Fonnte API (https://fonnte.com).
 *
 * Setup:
 * 1. Daftar di fonnte.com, hubungkan nomor WA device
 * 2. Ambil API Token dari dashboard Fonnte
 * 3. Set FONNTE_TOKEN di file .env
 */
class WhatsAppService
{
    protected string $apiUrl = 'https://api.fonnte.com/send';
    protected ?string $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    /**
     * Kirim pesan WhatsApp generik.
     *
     * @return array{success: bool, response: string}
     */
    public function send(string $phoneNumber, string $message): array
    {
        if (empty($this->token)) {
            Log::warning('Fonnte token belum diset di .env (FONNTE_TOKEN). Pesan WA tidak terkirim.');
            return [
                'success' => false,
                'response' => 'FONNTE_TOKEN belum dikonfigurasi.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->asForm()->post($this->apiUrl, [
                'target' => $this->formatPhoneNumber($phoneNumber),
                'message' => $message,
                'countryCode' => '62',
            ]);

            $body = $response->json();

            return [
                'success' => $response->successful() && ($body['status'] ?? false) !== false,
                'response' => json_encode($body),
            ];
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim WhatsApp via Fonnte: ' . $e->getMessage());

            return [
                'success' => false,
                'response' => $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim notifikasi WA saat pesanan baru diterima sistem.
     */
    public function sendOrderReceived(Order $order): array
    {
        $message = "Halo {$order->customer_name}! 👋\n\n"
            . "Pesanan Anda berhasil diterima dengan No. Antrean *#{$order->queue_number}*.\n\n"
            . "📍 Meja: {$order->table?->label}\n"
            . "💰 Total: {$order->formatted_total}\n"
            . "⏱ Estimasi: ±{$order->estimated_minutes} menit\n\n"
            . "Pesanan Anda akan segera diproses oleh chef kami. Terima kasih! 🍽️";

        return $this->send($order->whatsapp_number, $message);
    }

    /**
     * Kirim notifikasi WA saat status pesanan berubah (dimasak/disajikan).
     */
    public function sendStatusUpdate(Order $order): array
    {
        $statusMessages = [
            'dimasak' => "🔥 Pesanan Anda No. Antrean *#{$order->queue_number}* sedang *dimasak* oleh chef kami. Mohon tunggu sebentar ya!",
            'disajikan' => "✅ Pesanan Anda No. Antrean *#{$order->queue_number}* sudah *disajikan* ke meja {$order->table?->label}. Selamat menikmati! 🍽️",
            'dibatalkan' => "❌ Mohon maaf, pesanan Anda No. Antrean *#{$order->queue_number}* dibatalkan. Silakan hubungi staff kami untuk informasi lebih lanjut.",
        ];

        $message = $statusMessages[$order->status] ?? null;

        if (! $message) {
            return ['success' => false, 'response' => 'Status tidak memiliki template pesan.'];
        }

        return $this->send($order->whatsapp_number, $message);
    }

    /**
     * Pastikan nomor dalam format yang diterima Fonnte (62xxx, tanpa +/spasi).
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        if (! str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
