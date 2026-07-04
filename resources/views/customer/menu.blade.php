@extends('layouts.customer')

@section('title', 'Menu - RasaNusantara')

@section('content')
<div x-data="menuPage()" x-init="init()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-28 overflow-x-hidden">

    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-emerald-600 to-emerald-700 text-white rounded-3xl p-5 sm:p-6 mb-6 shadow-lg">
        <h2 class="text-xl sm:text-2xl font-bold mb-2">Kuliner Khas Dapur Nusantara</h2>
        <p class="text-emerald-100 text-sm leading-relaxed">
            Pesan langsung dari kenyamanan meja Anda. Antrean diproses real-time oleh chef dapur kami.
        </p>
    </section>

    {{-- Search Bar --}}
    <div class="relative mb-5">
        <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
        </div>
        <input
            type="text"
            x-model="searchQuery"
            @input="filterMenus()"
            placeholder="Cari menu makanan, minuman, cemilan..."
            class="w-full pl-10 pr-10 py-3 rounded-2xl border border-emerald-200 bg-white text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
        >
        <button type="button" x-show="searchQuery" @click="searchQuery = ''; filterMenus()"
            class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Filter Kategori --}}
    <div class="flex gap-2 overflow-x-auto pb-2 mb-5 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-hide">
        <button
            type="button"
            @click="activeCategory = null; filterMenus()"
            :class="activeCategory === null ? 'chip chip-active' : 'chip chip-inactive'"
            class="flex-shrink-0"
        >
            Semua Menu
        </button>
        @foreach ($categories as $category)
            <button
                type="button"
                @click="activeCategory = {{ $category->id }}; filterMenus()"
                :class="activeCategory === {{ $category->id }} ? 'chip chip-active' : 'chip chip-inactive'"
                class="flex-shrink-0"
            >
                {{ $category->name }} ({{ $menus->get($category->id, collect())->count() }})
            </button>
        @endforeach
    </div>

    {{-- Hasil pencarian kosong --}}
    <div x-show="filteredMenus.length === 0 && searchQuery !== ''" class="text-center py-14">
        <div class="text-5xl mb-3">🔍</div>
        <p class="text-slate-500 font-medium">Tidak ada menu untuk "<span class="text-emerald-600" x-text="searchQuery"></span>"</p>
        <p class="text-slate-400 text-sm mt-1">Coba kata kunci lain</p>
    </div>

    {{-- Grid Menu --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4">
        @foreach ($categories as $category)
            @foreach ($menus->get($category->id, collect()) as $menu)
                <div
                    x-show="isVisible({{ $menu->id }}, {{ $category->id }})"
                    x-transition:enter="transition-opacity duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="card relative overflow-hidden flex flex-col transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5"
                >
                    <div class="aspect-square bg-emerald-50 relative overflow-hidden group">
                        <img
                            src="{{ $menu->photo_url }}"
                            alt="{{ $menu->name }}"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                        @if (! $menu->isAvailable())
                            <div class="absolute inset-0 bg-slate-900/60 flex items-center justify-center pointer-events-none z-10">
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

                    <div class="p-3 sm:p-4 flex flex-col flex-1">
                        <div class="flex items-center justify-between mb-2 gap-1">
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

                        {{-- Rating Bintang --}}
                        <div class="flex items-center gap-1 mb-1.5 flex-wrap">
                            <div class="flex gap-0.5">
                                @for ($star = 1; $star <= 5; $star++)
                                    <span class="text-xs {{ $star <= round($menu->avg_rating) ? 'text-amber-400' : 'text-slate-200' }}">★</span>
                                @endfor
                            </div>
                            <span class="text-xs text-slate-400">
                                @if ($menu->rating_count > 0)
                                    {{ number_format($menu->avg_rating, 1) }} ({{ $menu->rating_count }})
                                @else
                                    Belum ada rating
                                @endif
                            </span>
                        </div>

                        @if ($menu->description)
                            <p class="text-slate-500 text-xs leading-relaxed line-clamp-2 mb-2">
                                {{ $menu->description }}
                            </p>
                        @endif

                        <p class="text-emerald-700 font-bold text-sm mb-2">{{ $menu->formatted_price }}</p>

                        {{-- Tombol aksi: flex-nowrap supaya selalu sejajar kanan-kiri,
                             tidak pindah baris meski card menyempit --}}
                        <div class="relative z-10 flex flex-nowrap items-stretch gap-1.5 mt-auto">
                            <button type="button"
                                @click="openRating({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->avg_rating }}, {{ $menu->rating_count }})"
                                class="relative z-10 flex items-center justify-center gap-1 px-2 py-1.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-600 text-xs font-medium hover:bg-amber-100 active:scale-95 transition flex-shrink-0 whitespace-nowrap cursor-pointer">
                                <span>★</span> Nilai
                            </button>

                            <button
                                type="button"
                                @click="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})"
                                :disabled="{{ $menu->isAvailable() ? 'false' : 'true' }}"
                                class="btn-primary relative z-10 text-xs py-2.5 flex-1 min-w-0 whitespace-nowrap overflow-hidden shadow-sm hover:shadow-md disabled:shadow-none disabled:opacity-40 disabled:cursor-not-allowed active:scale-95 transition-transform cursor-pointer"
                            >
                                + Tambah
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>

    @include('customer.partials.cart-drawer')
    @include('customer.partials.order-success-modal')
    @include('customer.partials.rating-modal')
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js" defer></script>
<script>
// Data flat menu untuk search/filter (dari MenuController@index)
const ALL_MENUS = @json($menusForJs);

function menuPage() {
    return {
        // ---- Filter & Search ----
        activeCategory: null,
        searchQuery: '',
        filteredMenus: ALL_MENUS.map(m => m.id),

        // ---- Cart ----
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

        // ---- Rating ----
        ratingOpen: false,
        ratingMenuId: null,
        ratingMenuName: '',
        ratingValue: 0,
        ratingHover: 0,
        ratingAvg: 0,
        ratingCount: 0,
        ratingSubmitting: false,
        ratingSuccess: false,

        // ====== INIT ======
        init() {
            // FIX: null-check supaya tidak error saat elemen belum ada,
            // yang sebelumnya bikin SEMUA @click 
            const navCartBtn = document.getElementById('nav-cart-btn');
            if (navCartBtn) {
                navCartBtn.addEventListener('click', () => {
                    this.cartOpen = true;
                });
            } else {
                console.warn('Elemen #nav-cart-btn tidak ditemukan di layout.');
            }
        },

        // ====== SEARCH & FILTER ======
        filterMenus() {
            const q = this.searchQuery.toLowerCase().trim();
            this.filteredMenus = ALL_MENUS
                .filter(m => {
                    const matchCategory = this.activeCategory === null || m.category_id === this.activeCategory;
                    const matchSearch = q === '' ||
                        m.name.toLowerCase().includes(q) ||
                        (m.description && m.description.toLowerCase().includes(q));
                    return matchCategory && matchSearch;
                })
                .map(m => m.id);
        },
        isVisible(menuId, categoryId) {
            return this.filteredMenus.includes(menuId);
        },

        // ====== CART ======
        addToCart(menuId, name, price) {
            const existing = this.cart.find(item => item.menuId === menuId);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({ menuId, name, price, qty: 1 });
            }
            this.updateBadge();
        },
        increment(item) { item.qty++; this.updateBadge(); },
        decrement(item) {
            item.qty--;
            if (item.qty <= 0) this.cart = this.cart.filter(i => i.menuId !== item.menuId);
            this.updateBadge();
        },
        updateBadge() {
    const badge = document.getElementById('cart-badge');
    if (!badge) return;
    const totalQty = this.cart.reduce((sum, i) => sum + i.qty, 0);
    if (totalQty > 0) {
        badge.style.display = 'flex';
        badge.textContent = totalQty;
    } else {
        badge.style.display = 'none';
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

        // ====== RATING ======
        openRating(menuId, name, avg, count) {
            this.ratingMenuId   = menuId;
            this.ratingMenuName = name;
            this.ratingAvg      = avg;
            this.ratingCount    = count;
            this.ratingValue    = 0;
            this.ratingHover    = 0;
            this.ratingSuccess  = false;
            this.ratingOpen     = true;
        },
        starLabel(star) {
            const labels = ['', 'Buruk', 'Kurang', 'Cukup', 'Bagus', 'Sangat Bagus'];
            return labels[star] || '';
        },
        async submitRating() {
            if (!this.ratingValue) return;
            this.ratingSubmitting = true;
            try {
                const res = await fetch(`/menu/${this.ratingMenuId}/rating`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ rating: this.ratingValue }),
                });
                const data = await res.json();
                if (data.success) {
                    this.ratingAvg   = data.avg_rating;
                    this.ratingCount = data.rating_count;
                    this.ratingSuccess = true;
                    setTimeout(() => { this.ratingOpen = false; }, 1500);
                }
            } catch (e) {
                // silent fail
            } finally {
                this.ratingSubmitting = false;
            }
        },
    };
}
</script>
@endpush