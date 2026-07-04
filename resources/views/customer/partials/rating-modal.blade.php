{{-- Modal Rating Menu --}}
<div
    x-show="ratingOpen"
    x-cloak
    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
    style="display: none;"
>
    <div @click="ratingOpen = false"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="absolute inset-0 bg-slate-900/50">
    </div>

    <div
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="relative bg-white rounded-3xl w-full max-w-sm p-6 z-10 text-center"
    >
        {{-- Sukses state --}}
        <template x-if="ratingSuccess">
            <div class="py-4">
                <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="font-bold text-slate-800">Terima kasih!</p>
                <p class="text-slate-500 text-sm mt-1">Rating Anda sudah tersimpan.</p>
            </div>
        </template>

        {{-- Form rating --}}
        <template x-if="!ratingSuccess">
            <div>
                <button type="button" @click="ratingOpen = false"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <p class="text-slate-400 text-xs mb-1">Beri penilaian untuk</p>
                <h3 class="font-bold text-slate-800 text-base mb-1" x-text="ratingMenuName"></h3>

                {{-- Rating sekarang --}}
                <p class="text-xs text-slate-400 mb-4">
                    Saat ini:
                    <span class="text-amber-500 font-medium" x-text="ratingAvg > 0 ? '★ ' + Number(ratingAvg).toFixed(1) + ' (' + ratingCount + ' ulasan)' : 'Belum ada rating'"></span>
                </p>

                {{-- Bintang interaktif --}}
                <div class="flex justify-center gap-2 mb-3">
                    <template x-for="star in [1,2,3,4,5]" :key="star">
                        <button type="button"
                            @mouseenter="ratingHover = star"
                            @mouseleave="ratingHover = 0"
                            @click="ratingValue = star"
                            class="text-4xl transition-transform hover:scale-110 focus:outline-none"
                            :class="star <= (ratingHover || ratingValue) ? 'text-amber-400' : 'text-slate-200'">
                            ★
                        </button>
                    </template>
                </div>

                {{-- Label bintang --}}
                <p class="text-emerald-700 font-semibold text-sm h-5 mb-5"
                    x-text="starLabel(ratingHover || ratingValue)">
                </p>

                <button type="button"
                    @click="submitRating()"
                    :disabled="!ratingValue || ratingSubmitting"
                    class="btn-primary w-full disabled:opacity-40 disabled:cursor-not-allowed">
                    <span x-show="!ratingSubmitting">Kirim Penilaian</span>
                    <span x-show="ratingSubmitting">Menyimpan...</span>
                </button>
            </div>
        </template>
    </div>
</div>
