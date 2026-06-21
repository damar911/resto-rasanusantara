@extends('layouts.customer')

@section('title', 'Lacak Antrean #' . $order->queue_number . ' - RasaNusantara')

@section('content')
<div x-data="trackPage()" x-init="init()">

    <div class="card p-6 mb-5 text-center">
        <p class="text-slate-500 text-sm mb-1">Status Dapur</p>
        <h2 class="text-3xl font-bold text-emerald-700 mb-1">Antrean #<span x-text="order.queue_number"></span></h2>
        <p class="text-slate-400 text-xs">ID: #{{ $order->id }}</p>
    </div>

    {{-- Progress Bar 3 Tahap --}}
    <div class="card p-6 mb-5">
        <div class="flex items-center justify-between mb-6">
            <template x-for="(step, index) in steps" :key="step.key">
                <div class="flex items-center flex-1 last:flex-none">
                    <div class="flex flex-col items-center">
                        <div :class="order.status_step >= index && order.status !== 'dibatalkan' ? 'step-dot step-dot-done' : 'step-dot step-dot-pending'">
                            <svg x-show="order.status_step > index || (order.status_step === index && order.status === 'disajikan')" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            <span x-show="!(order.status_step > index || (order.status_step === index && order.status === 'disajikan'))" x-text="index + 1"></span>
                        </div>
                        <span class="text-xs font-medium mt-2 text-center" :class="order.status_step >= index ? 'text-emerald-700' : 'text-slate-400'" x-text="step.label"></span>
                    </div>
                    <div x-show="index < steps.length - 1" class="flex-1 h-0.5 mx-2" :class="order.status_step > index ? 'bg-emerald-600' : 'bg-slate-200'"></div>
                </div>
            </template>
        </div>

        <div class="text-center bg-emerald-50 rounded-2xl py-4">
            <p class="text-emerald-800 font-semibold" x-text="statusDescription"></p>
            <p x-show="order.status !== 'disajikan' && order.status !== 'dibatalkan'" class="text-slate-500 text-xs mt-1">
                Perkiraan Selesai <span x-text="order.estimated_minutes"></span> Menit
            </p>
        </div>
    </div>

    {{-- Detail Pesanan --}}
    <div class="card p-5 mb-5">
        <h3 class="font-semibold text-slate-700 mb-3">Detail Pesanan</h3>
        <div class="space-y-2 text-sm mb-3">
            <div class="flex justify-between"><span class="text-slate-500">Nama Pemesan</span><span class="font-medium">{{ $order->customer_name }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Meja</span><span class="font-medium">{{ $order->table->label }}</span></div>
        </div>
        <div class="border-t border-emerald-100 pt-3 space-y-2">
            @foreach ($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">{{ $item->quantity }}x {{ $item->menu_name }}</span>
                    <span class="font-medium">{{ $item->formatted_subtotal }}</span>
                </div>
            @endforeach
        </div>
        <div class="border-t border-emerald-100 pt-3 mt-3 flex justify-between">
            <span class="font-semibold text-slate-700">Total Pembayaran</span>
            <span class="font-bold text-emerald-700" x-text="order.formatted_total"></span>
        </div>
    </div>

    <a href="{{ route('customer.menu') }}" class="btn-secondary w-full text-center block">
        Kembali ke Menu
    </a>
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
    });
</script>
<script>
function trackPage() {
    return {
        order: {
    id: {{ $order->id }},
    queue_number: "{{ $order->queue_number }}",
    status: "{{ $order->status }}",
    status_step: {{ $order->status_step }},
    estimated_minutes: {{ $order->estimated_minutes }},
    formatted_total: "{{ $order->formatted_total }}"
},
        steps: [
            { key: 'diterima', label: 'Diterima' },
            { key: 'dimasak', label: 'Dimasak' },
            { key: 'disajikan', label: 'Disajikan' },
        ],

        init() {
            // Subscribe ke channel order khusus untuk order ini
            window.echoInstance.channel('order.{{ $order->id }}')
                .listen('.status.updated', (data) => {
                    if (data.order_id === this.order.id) {
                        this.order.status = data.status;
                        this.order.status_step = data.status_step;
                        this.order.estimated_minutes = data.estimated_minutes;
                        this.order.formatted_total = data.formatted_total;
                    }
                });

            // Fallback polling tiap 15 detik jika WebSocket gagal connect
            setInterval(() => this.fetchStatus(), 15000);
        },

        async fetchStatus() {
            try {
                const res = await fetch('{{ route('order.status', $order->id) }}');
                const data = await res.json();
                this.order.status = data.status;
                this.order.status_step = data.status_step;
                this.order.estimated_minutes = data.estimated_minutes;
            } catch (e) { /* silent fail, akan dicoba lagi */ }
        },

        get statusDescription() {
            const map = {
                diterima: 'Pesanan Anda telah masuk antrean sistem.',
                dimasak: 'Pesanan Anda sedang diproses Chef kami.',
                disajikan: 'Pesanan Anda telah diantar menuju meja Anda. Selamat menikmati!',
                dibatalkan: 'Mohon maaf, pesanan Anda dibatalkan.',
            };
            return map[this.order.status] ?? '';
        },
    };
}
</script>
@endpush
