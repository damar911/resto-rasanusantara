@extends('layouts.admin')

@section('title', 'Manajemen Antrean - RasaNusantara')

@section('content')
<div x-data="queuePage()" x-init="init()">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Antrean Dapur</h1>
            <p class="text-slate-500 text-sm">Kelola status pesanan secara real-time</p>
        </div>
        <span class="bg-emerald-100 text-emerald-700 text-sm font-medium px-3 py-1.5 rounded-full" x-text="orders.filter(o => o.status === 'diterima' || o.status === 'dimasak').length + ' Aktif'"></span>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        {{-- Kolom: Diterima --}}
        <div>
            <h3 class="text-sm font-semibold text-blue-600 mb-3 px-1">Diterima</h3>
            <div class="space-y-3">
                <template x-for="order in orders.filter(o => o.status === 'diterima')" :key="order.id">
                    <div class="card p-4">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-slate-800">#<span x-text="order.queue_number"></span></span>
                            <span class="text-xs text-slate-400" x-text="order.created_at"></span>
                        </div>
                        <p class="text-sm text-slate-600 mb-1" x-text="order.customer_name"></p>
                        <p class="text-xs text-slate-400 mb-2" x-text="order.table_label + ' · ' + order.items_count + ' item · ' + order.formatted_total"></p>
                        <ul class="border-t border-slate-100 pt-2 mb-3 space-y-1">
                            <template x-for="(item, i) in order.items" :key="i">
                                <li class="flex gap-2 text-sm text-slate-700">
                                    <span class="font-bold text-emerald-700 w-8 flex-shrink-0" x-text="item.quantity + '×'"></span>
                                    <span x-text="item.name"></span>
                                </li>
                            </template>
                        </ul>
                        <button type="button" @click="updateStatus(order, 'dimasak')" class="btn-primary w-full text-xs py-2">
                            Mulai Masak &rarr;
                        </button>
                    </div>
                </template>
                <p x-show="orders.filter(o => o.status === 'diterima').length === 0" class="text-slate-400 text-xs text-center py-6">Tidak ada pesanan baru</p>
            </div>
        </div>

        {{-- Kolom: Dimasak --}}
        <div>
            <h3 class="text-sm font-semibold text-amber-600 mb-3 px-1">Dimasak</h3>
            <div class="space-y-3">
                <template x-for="order in orders.filter(o => o.status === 'dimasak')" :key="order.id">
                    <div class="card p-4">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-slate-800">#<span x-text="order.queue_number"></span></span>
                            <span class="text-xs text-slate-400" x-text="order.created_at"></span>
                        </div>
                        <p class="text-sm text-slate-600 mb-1" x-text="order.customer_name"></p>
                        <p class="text-xs text-slate-400 mb-2" x-text="order.table_label + ' · ' + order.items_count + ' item · ' + order.formatted_total"></p>
                        <ul class="border-t border-slate-100 pt-2 mb-3 space-y-1">
                            <template x-for="(item, i) in order.items" :key="i">
                                <li class="flex gap-2 text-sm text-slate-700">
                                    <span class="font-bold text-emerald-700 w-8 flex-shrink-0" x-text="item.quantity + '×'"></span>
                                    <span x-text="item.name"></span>
                                </li>
                            </template>
                        </ul>
                        <button type="button" @click="updateStatus(order, 'disajikan')" class="btn-primary w-full text-xs py-2">
                            Sajikan &rarr;
                        </button>
                    </div>
                </template>
                <p x-show="orders.filter(o => o.status === 'dimasak').length === 0" class="text-slate-400 text-xs text-center py-6">Tidak ada yang dimasak</p>
            </div>
        </div>

        {{-- Kolom: Disajikan --}}
        <div>
            <h3 class="text-sm font-semibold text-emerald-600 mb-3 px-1">Disajikan</h3>
            <div class="space-y-3">
                <template x-for="order in orders.filter(o => o.status === 'disajikan')" :key="order.id">
                    <div class="card p-4 opacity-75">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-slate-800">#<span x-text="order.queue_number"></span></span>
                            <span class="text-xs text-slate-400" x-text="order.created_at"></span>
                        </div>
                        <p class="text-sm text-slate-600 mb-1" x-text="order.customer_name"></p>
                        <p class="text-xs text-slate-400 mb-2" x-text="order.table_label + ' · ' + order.items_count + ' item · ' + order.formatted_total"></p>
                        <ul class="border-t border-slate-100 pt-2 mb-0 space-y-1">
                            <template x-for="(item, i) in order.items" :key="i">
                                <li class="flex gap-2 text-sm text-slate-700">
                                    <span class="font-bold text-emerald-700 w-8 flex-shrink-0" x-text="item.quantity + '×'"></span>
                                    <span x-text="item.name"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
                <p x-show="orders.filter(o => o.status === 'disajikan').length === 0" class="text-slate-400 text-xs text-center py-6">Belum ada yang selesai</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js" defer></script>
<script type="module">
    import Echo from 'https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.esm.js';
    import Pusher from 'https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js';

    window.Pusher = Pusher;

    window.echoInstance = new Echo({
        broadcaster: 'reverb',
        key: '{{ config('broadcasting.connections.reverb.key') }}',
        wsHost: '{{ config('reverb.servers.reverb.hostname', request()->getHost()) }}',
        wsPort: {{ config('reverb.servers.reverb.port', 8080) }},
        wssPort: {{ config('reverb.servers.reverb.port', 8080) }},
        forceTLS: {{ config('reverb.apps.apps.0.options.useTLS') ? 'true' : 'false' }},
        enabledTransports: ['ws', 'wss'],
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        },
    });
</script>
<script>
function queuePage() {
    return {
        orders: @json($orders),

        init() {
            window.echoInstance.private('kitchen-dashboard')
                .listen('.order.created', (data) => {
                    this.orders.push({
                        id: data.order_id,
                        queue_number: data.queue_number,
                        customer_name: data.customer_name,
                        table_label: data.table_label,
                        status: data.status,
                        items_count: data.items_count,
                        formatted_total: data.formatted_total,
                        items: data.items || [],
                        created_at: data.created_at,
                    });
                })
                .listen('.status.updated', (data) => {
                    const order = this.orders.find(o => o.id === data.order_id);
                    if (order) {
                        order.status = data.status;
                    }
                });
        },

        async updateStatus(order, newStatus) {
            try {
                const res = await fetch(`/admin/queue/${order.id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus }),
                });

                if (res.ok) {
                    order.status = newStatus;
                }
            } catch (e) {
                alert('Gagal memperbarui status. Silakan coba lagi.');
            }
        },
    };
}
</script>
@endpush
