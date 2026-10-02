@extends('layouts.admin')

@section('title', 'Manajemen Meja - RasaNusantara')

@section('content')
<div x-data="tablesPage()" x-init="init()">

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Meja</h1>
            <p class="text-slate-500 text-sm">
                <span x-text="tables.filter(t => !t.is_occupied).length"></span> kosong &middot;
                <span x-text="tables.filter(t => t.is_occupied).length"></span> terisi
            </p>
        </div>
        <a href="{{ route('admin.tables.qr') }}" target="_blank" class="btn-secondary">Cetak QR Meja</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <template x-for="table in tables" :key="table.id">
            <div class="card p-4 border-2"
                 :class="table.is_occupied ? 'border-red-200 bg-red-50/40' : 'border-emerald-200'">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-slate-800" x-text="table.label"></h3>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full"
                          :class="table.is_occupied ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-700'"
                          x-text="table.is_occupied ? 'Terisi' : 'Kosong'"></span>
                </div>

                <div class="text-xs text-slate-500 min-h-[2.25rem] mb-3">
                    <template x-if="table.is_occupied">
                        <p>
                            <span x-text="table.occupant_name || 'Pelanggan'"></span>
                            <span x-show="table.occupied_since"> &middot; sejak <span x-text="table.occupied_since"></span></span>
                        </p>
                    </template>
                    <template x-if="!table.is_occupied">
                        <p>Bisa dipesan pelanggan.</p>
                    </template>
                </div>

                <button type="button" @click="table.is_occupied ? setStatus(table, 'free') : setStatus(table, 'occupy')"
                        :disabled="busyId === table.id"
                        :class="table.is_occupied ? 'btn-primary' : 'btn-secondary'"
                        class="w-full text-sm"
                        x-text="table.is_occupied ? 'Kosongkan Meja' : 'Tandai Terisi'"></button>
            </div>
        </template>
    </div>

    <p x-show="tables.length === 0" class="text-slate-400 text-sm text-center py-10">Belum ada meja aktif.</p>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js" defer></script>
<script>
    function tablesPage() {
        return {
            tables: @json($tables),
            busyId: null,

            init() {
                // Segarkan status meja tiap 8 detik (meja terisi otomatis saat pelanggan membayar)
                setInterval(() => this.refresh(), 8000);
            },

            async refresh() {
                if (this.busyId) return;
                try {
                    const res = await fetch('{{ route('admin.tables.status') }}', { headers: { 'Accept': 'application/json' } });
                    if (res.ok) this.tables = await res.json();
                } catch (e) {}
            },

            async setStatus(table, action) {
                if (action === 'free' && !confirm(`Kosongkan ${table.label}? Pastikan pelanggan sudah selesai.`)) return;
                this.busyId = table.id;
                try {
                    await fetch(`/admin/tables/${table.id}/${action}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                } finally {
                    this.busyId = null;
                    await this.refresh();
                }
            },
        };
    }
</script>
@endpush
