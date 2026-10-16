@extends('layouts.admin')

@section('title', 'Katalog Menu - RasaNusantara')

@section('content')
<div x-data="menuManagement()">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Katalog Menu Dapur</h1>
            <p class="text-slate-500 text-sm">Kelola data menu makanan, minuman, dan cemilan</p>
        </div>
        <button type="button" @click="openCreateModal()" class="btn-primary">
            + Tambah Menu
        </button>
    </div>

    {{-- Filter Kategori --}}
    <div class="flex gap-2 mb-5">
        <button type="button" @click="filterCategory = null" :class="filterCategory === null ? 'chip-active' : 'chip-inactive'" class="chip">Semua</button>
        @foreach ($categories as $category)
            <button type="button" @click="filterCategory = {{ $category->id }}" :class="filterCategory === {{ $category->id }} ? 'chip-active' : 'chip-inactive'" class="chip">{{ $category->name }}</button>
        @endforeach
    </div>

    {{-- Tabel Menu --}}
    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-emerald-50 text-slate-600 text-left">
                <tr>
                    <th class="px-4 py-3">Menu</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-emerald-50">
                @foreach ($menus as $menu)
                    <tr x-show="filterCategory === null || filterCategory === {{ $menu->category_id }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $menu->photo_url }}" class="w-10 h-10 rounded-lg object-cover bg-emerald-50" alt="{{ $menu->name }}">
                                <span class="font-medium text-slate-800">{{ $menu->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $menu->category->name }}</td>
                        <td class="px-4 py-3">
                            @php
                                $promo = $menu->promoStatus();
                                $editData = [
                                    'id' => $menu->id,
                                    'category_id' => $menu->category_id,
                                    'name' => $menu->name,
                                    'description' => $menu->description,
                                    'price' => $menu->price,
                                    'promo_price' => $menu->promo_price,
                                    'promo_starts_at' => $menu->promo_starts_at?->format('Y-m-d\TH:i'),
                                    'promo_ends_at' => $menu->promo_ends_at?->format('Y-m-d\TH:i'),
                                    'availability' => $menu->availability,
                                ];
                            @endphp
                            @if ($promo === 'active')
                                <span class="text-slate-400 text-xs line-through block">{{ $menu->formatted_price }}</span>
                                <span class="font-medium text-red-600">{{ $menu->formatted_effective_price }}</span>
                                <span class="text-[10px] font-semibold bg-red-100 text-red-600 px-2 py-0.5 rounded-full ml-1">Promo aktif</span>
                            @else
                                <span class="font-medium text-emerald-700">{{ $menu->formatted_price }}</span>
                                @if ($promo === 'scheduled')
                                    <span class="text-[10px] font-semibold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full ml-1">Promo terjadwal</span>
                                @elseif ($promo === 'expired')
                                    <span class="text-[10px] font-semibold bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full ml-1">Promo berakhir</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $menu->isAvailable() ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                                {{ $menu->isAvailable() ? 'Tersedia' : 'Habis' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <button type="button" @click="openEditModal(@js($editData))" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">Edit</button>
                            <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" class="inline" onsubmit="return confirm('Hapus menu {{ addslashes($menu->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('admin.menus.modal-form')
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js" defer></script>
<script>
function menuManagement() {
    return {
        filterCategory: null,
        modalOpen: false,
        isEdit: false,
        form: {
            id: null,
            category_id: '',
            name: '',
            description: '',
            price: '',
            promo_price: '',
            promo_starts_at: '',
            promo_ends_at: '',
            availability: 'available',
        },

        openCreateModal() {
            this.isEdit = false;
            this.form = { id: null, category_id: '', name: '', description: '', price: '', promo_price: '', promo_starts_at: '', promo_ends_at: '', availability: 'available' };
            this.modalOpen = true;
        },

        openEditModal(menu) {
            this.isEdit = true;
            this.form = {
                id: menu.id,
                category_id: menu.category_id,
                name: menu.name,
                description: menu.description ?? '',
                price: menu.price,
                promo_price: menu.promo_price ?? '',
                promo_starts_at: menu.promo_starts_at ?? '',
                promo_ends_at: menu.promo_ends_at ?? '',
                availability: menu.availability,
            };
            this.modalOpen = true;
        },
    };
}
</script>
@endpush