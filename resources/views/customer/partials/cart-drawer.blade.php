{{-- Cart Drawer (Keranjang Pemesanan) --}}
<div
    x-show="cartOpen"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div
        x-show="cartOpen"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="cartOpen = false"
        class="absolute inset-0 bg-slate-900/50"
    ></div>

    {{-- Drawer Content --}}
    <div
        x-show="cartOpen"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white w-full max-w-md rounded-3xl max-h-[90vh] overflow-y-auto shadow-xl z-10"
    >
        <div class="sticky top-0 bg-white border-b border-emerald-100 px-5 py-4 flex items-center justify-between rounded-t-3xl z-10">
            <h3 class="font-bold text-slate-800">Keranjang Pemesanan Anda</h3>
            <button type="button" @click="cartOpen = false" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-5">
            {{-- Empty state --}}
            <template x-if="cart.length === 0">
                <p class="text-center text-slate-400 text-sm py-8">Keranjang masih kosong. Pilih menu favorit Anda!</p>
            </template>

            {{-- Cart items --}}
            <div class="space-y-3 mb-5">
                <template x-for="item in cart" :key="item.menuId">
                    <div class="flex items-center justify-between gap-3 card p-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-slate-800 truncate" x-text="item.name"></p>
                            <p class="text-emerald-700 text-sm font-semibold" x-text="formatRupiah(item.price)"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="decrement(item)" class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center">-</button>
                            <span class="w-6 text-center text-sm font-semibold" x-text="item.qty"></span>
                            <button type="button" @click="increment(item)" class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center">+</button>
                        </div>
                    </div>
                </template>
            </div>

            <template x-if="cart.length > 0">
                <div>
                    {{-- Subtotal --}}
                    <div class="flex items-center justify-between py-3 border-t border-emerald-100 mb-4">
                        <span class="font-medium text-slate-600">Subtotal Pembayaran</span>
                        <span class="font-bold text-lg text-emerald-700" x-text="formatRupiah(subtotal)"></span>
                    </div>

                    {{-- Form data pemesan --}}
                    <div class="space-y-3 mb-4">
                        <div>
                            <label class="text-xs font-medium text-slate-600 mb-1 block">Nama Pemesan</label>
                            <input type="text" x-model="customerName" placeholder="Masukkan nama Anda" class="input-field">
                        </div>

                        <div>
                            <label class="text-xs font-medium text-slate-600 mb-1 block">Nomor Meja</label>
                            <select x-model="tableId" class="input-field">
                                <option value="">Pilih Meja</option>
                                <template x-for="table in tables" :key="table.id">
                                    <option :value="table.id" :disabled="!table.available" x-text="table.available ? table.label : table.label + ' (Terisi)'"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-medium text-slate-600 mb-1 block">Nomor WhatsApp Aktif</label>
                            <input type="tel" x-model="whatsappNumber" @change="loadTables()" placeholder="08xxxxxxxxxx" class="input-field">
                            <p class="text-xs text-slate-400 mt-1">Sistem akan mengirim notifikasi pesanan ke nomor WA ini.</p>
                        </div>

                        <div class="rounded-xl bg-emerald-50 border border-emerald-100 px-3 py-2.5">
                            <p class="text-xs font-medium text-slate-600">Metode Pembayaran</p>
                            <p class="text-sm font-semibold text-emerald-700">Pembayaran Digital</p>
                            <p class="text-xs text-slate-500 mt-0.5">QRIS, Transfer Bank / Virtual Account, dan e-wallet. Pilih metode setelah menekan tombol bayar.</p>
                        </div>
                    </div>

                    <template x-if="errorMessage">
                        <p class="text-red-600 text-sm bg-red-50 rounded-lg px-3 py-2 mb-3" x-text="errorMessage"></p>
                    </template>

                    <div class="flex gap-3">
                        <button type="button" @click="cartOpen = false" class="btn-secondary flex-1">Kembali</button>
                        <button type="button" @click="submitOrder()" :disabled="submitting" class="btn-primary flex-1">
                            <span x-show="!submitting">Bayar Sekarang</span>
                            <span x-show="submitting">Memproses...</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
