@extends('layouts.customer')

@section('title', 'Menu - RasaNusantara')

@section('content')
<div x-data="menuPage()" x-init="init()">

    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-emerald-600 to-emerald-700 text-white rounded-3xl p-6 mb-6 shadow-lg">
        <h2 class="text-2xl font-bold mb-2">Kuliner Khas Dapur Nusantara</h2>
        <p class="text-emerald-100 text-sm leading-relaxed">
            Pesan langsung dari kenyamanan meja Anda. Antrean diproses real-time oleh chef dapur kami.
        </p>
    </section>

    {{-- Filter Kategori --}}
    <div class="flex gap-2 overflow-x-auto pb-2 mb-5 -mx-1 px-1">
        <button
            type="button"
            @click="activeCategory = null"
            :class="activeCategory === null ? 'chip chip-active' : 'chip chip-inactive'"
        >
            Semua Menu
        </button>
        @foreach ($categories as $category)
            <button
                type="button"
                @click="activeCategory = {{ $category->id }}"
                :class="activeCategory === {{ $category->id }} ? 'chip chip-active' : 'chip chip-inactive'"
            >
                {{ $category->name }} ({{ $menus->get($category->id, collect())->count() }})
            </button>
        @endforeach
    </div>

    {{-- Grid Menu --}}
    {{-- Grid Menu --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
    @foreach ($categories as $category)
        @foreach ($menus->get($category->id, collect()) as $menu)
            <div
                x-show="activeCategory === null || activeCategory === {{ $category->id }}"
                class="card overflow-hidden flex flex-col transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5"
            >
                <div class="aspect-square bg-emerald-50 relative overflow-hidden group">
                    <img
                        src="{{ $menu->photo_url }}"
                        alt="{{ $menu->name }}"
                        class="w-full h-full object-cover"
                        loading="lazy"
                    >
                    @if (! $menu->isAvailable())
                        <div class="absolute inset-0 bg-slate-900/60 flex items-center justify-center">
                            <span class="bg-white text-slate-700 text-xs font-semibold px-3 py-1 rounded-full">Habis</span>
                        </div>
                    @endif

                    {{-- Popup detail saat hover (desktop only) --}}
                    <div class="absolute inset-0 z-10 flex flex-col items-center justify-center text-center p-3 bg-slate-900/85 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                        <h4 class="font-semibold text-sm mb-1.5">{{ $menu->name }}</h4>
                        @if ($menu->description)
                            <p class="text-xs text-slate-200 leading-relaxed line-clamp-4">
                                {{ $menu->description }}
                            </p>
                        @else
                            <p class="text-xs text-slate-400 italic">Belum ada deskripsi untuk menu ini.</p>
                        @endif
                        <p class="text-emerald-300 font-bold text-sm mt-2">{{ $menu->formatted_price }}</p>
                    </div>
                </div>

                <div class="p-4 flex flex-col flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 uppercase tracking-wide px-2 py-0.5 rounded-full">
                            {{ $category->name }}
                        </span>
                        <span class="flex items-center gap-1 text-[11px] font-medium {{ $menu->isAvailable() ? 'text-emerald-600' : 'text-red-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $menu->isAvailable() ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                            {{ $menu->isAvailable() ? 'Tersedia' : 'Habis' }}
                        </span>
                    </div>

                    <h3 class="font-bold text-sm text-slate-800 line-clamp-2 leading-snug mb-1 tracking-tight">
                        {{ $menu->name }}
                    </h3>

                    @if ($menu->description)
                        <p class="text-slate-500 text-xs leading-relaxed line-clamp-2 mb-2">
                            {{ $menu->description }}
                        </p>
                    @endif

                    <div class="flex items-center justify-between mt-auto pt-2">
                        <p class="text-emerald-700 font-bold text-sm">{{ $menu->formatted_price }}</p>
                    </div>

                    <button
                        type="button"
                        @click="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})"
                        :disabled="{{ $menu->isAvailable() ? 'false' : 'true' }}"
                        class="btn-primary text-xs py-2.5 mt-2 w-full shadow-sm hover:shadow-md disabled:shadow-none"
                    >
                        + Tambah
                    </button>
                </div>
            </div>
        @endforeach
    @endforeach
</div>

    @include('customer.partials.cart-drawer')
    @include('customer.partials.order-success-modal')
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js" defer></script>
<script>
function menuPage() {
    return {
        activeCategory: null,
        cartOpen: false,
        cart: [],
        tables: @json($tables->map(fn($t) => ['id' => $t->id, 'label' => $t->label])),
        customerName: '',
        tableId: '',
        whatsappNumber: '',
        paymentMethod: 'tunai',
        submitting: false,
        successOrder: null,
        errorMessage: '',

        init() {
            // Buka keranjang dari bottom nav
            document.getElementById('nav-cart-btn').addEventListener('click', () => {
                this.cartOpen = true;
            });
        },

        addToCart(menuId, name, price) {
            const existing = this.cart.find(item => item.menuId === menuId);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({ menuId, name, price, qty: 1 });
            }
            this.updateBadge();
        },

        increment(item) {
            item.qty++;
            this.updateBadge();
        },

        decrement(item) {
            item.qty--;
            if (item.qty <= 0) {
                this.cart = this.cart.filter(i => i.menuId !== item.menuId);
            }
            this.updateBadge();
        },

        updateBadge() {
            const badge = document.getElementById('cart-badge');
            const totalQty = this.cart.reduce((sum, i) => sum + i.qty, 0);
            if (totalQty > 0) {
                badge.classList.remove('hidden');
                badge.textContent = totalQty;
            } else {
                badge.classList.add('hidden');
            }
        },

        get subtotal() {
            return this.cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
        },

        formatRupiah(value) {
            return 'Rp ' + value.toLocaleString('id-ID');
        },

        async submitOrder() {
            this.errorMessage = '';

            if (this.cart.length === 0) {
                this.errorMessage = 'Keranjang masih kosong, silakan pilih menu terlebih dahulu.';
                return;
            }
            if (!this.customerName || !this.tableId || !this.whatsappNumber) {
                this.errorMessage = 'Mohon lengkapi nama, nomor meja, dan nomor WhatsApp.';
                return;
            }

            this.submitting = true;

            try {
                const response = await fetch('{{ route('order.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        customer_name: this.customerName,
                        table_id: this.tableId,
                        whatsapp_number: this.whatsappNumber,
                        payment_method: this.paymentMethod,
                        items: this.cart.map(i => ({ menu_id: i.menuId, quantity: i.qty })),
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    this.errorMessage = data.message || 'Gagal membuat pesanan. Silakan coba lagi.';
                    this.submitting = false;
                    return;
                }

                this.successOrder = data.order;
                this.cart = [];
                this.updateBadge();
                this.cartOpen = false;
            } catch (e) {
                this.errorMessage = 'Terjadi kesalahan koneksi. Silakan coba lagi.';
            } finally {
                this.submitting = false;
            }
        },
    };
}
</script>
@endpush
