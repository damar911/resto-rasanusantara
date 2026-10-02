{{-- Modal Hasil Pembayaran (state: paid | pending | unpaid) --}}
<div
    x-show="successOrder"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-5"
    style="display: none;"
>
    <div
        x-show="successOrder"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        @click="successOrder = null"
        class="absolute inset-0 bg-slate-900/60"
    ></div>

    <div
        x-show="successOrder"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="relative bg-white rounded-3xl p-6 max-w-sm w-full text-center z-10"
    >
        {{-- PAID --}}
        <template x-if="successOrder?.state === 'paid'">
            <div>
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="font-bold text-lg text-slate-800 mb-1">Pembayaran Berhasil!</h3>
                <p class="text-slate-500 text-sm mb-4">
                    No. Antrean Anda:
                    <span class="font-bold text-emerald-700 text-xl block mt-1" x-text="'#' + (successOrder?.queue_number ?? '')"></span>
                </p>
                <p class="text-slate-400 text-xs mb-5">
                    Notifikasi WhatsApp akan dikirim ke nomor Anda. Pantau status pesanan secara real-time.
                </p>
                <div class="flex flex-col gap-2">
                    <a :href="successOrder?.tracking_url" class="btn-primary w-full text-center">Lacak Antrean Saya</a>
                    <button type="button" @click="successOrder = null" class="btn-secondary w-full">Pesan Lagi</button>
                </div>
            </div>
        </template>

        {{-- PENDING (mis. Virtual Account dibuat, belum dibayar) --}}
        <template x-if="successOrder?.state === 'pending'">
            <div>
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-lg text-slate-800 mb-1">Menunggu Pembayaran</h3>
                <p class="text-slate-500 text-sm mb-5">
                    Selesaikan pembayaran Anda. Pesanan akan diproses dapur dan notifikasi WhatsApp dikirim setelah pembayaran diterima.
                </p>
                <div class="flex flex-col gap-2">
                    <button type="button" @click="payOrder()" class="btn-primary w-full">Lihat Instruksi Pembayaran</button>
                    <button type="button" @click="successOrder = null" class="btn-secondary w-full">Tutup</button>
                </div>
            </div>
        </template>

        {{-- UNPAID (popup ditutup / gagal) --}}
        <template x-if="successOrder?.state === 'unpaid'">
            <div>
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
                <h3 class="font-bold text-lg text-slate-800 mb-1">Pembayaran Belum Selesai</h3>
                <p class="text-slate-500 text-sm mb-5">
                    Pesanan Anda belum diproses karena pembayaran belum selesai. Anda dapat melanjutkan pembayaran sekarang.
                </p>
                <div class="flex flex-col gap-2">
                    <button type="button" @click="payOrder()" class="btn-primary w-full">Lanjutkan Pembayaran</button>
                    <button type="button" @click="successOrder = null" class="btn-secondary w-full">Tutup</button>
                </div>
            </div>
        </template>
    </div>
</div>
