{{-- Modal Sukses Pesanan --}}
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
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>

        <h3 class="font-bold text-lg text-slate-800 mb-1">Pesanan Berhasil Dibuat!</h3>
        <p class="text-slate-500 text-sm mb-4">
            No. Antrean Anda:
            <span class="font-bold text-emerald-700 text-xl block mt-1" x-text="'#' + (successOrder?.queue_number ?? '')"></span>
        </p>
        <p class="text-slate-400 text-xs mb-5">
            Notifikasi WhatsApp telah dikirim ke nomor Anda. Pantau status pesanan secara real-time.
        </p>

        <div class="flex flex-col gap-2">
            <a
                :href="successOrder?.tracking_url"
                class="btn-primary w-full text-center"
            >
                Lacak Antrean Saya
            </a>
            <button type="button" @click="successOrder = null" class="btn-secondary w-full">
                Pesan Lagi
            </button>
        </div>
    </div>
</div>
