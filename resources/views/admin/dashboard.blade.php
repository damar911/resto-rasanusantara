@extends('layouts.admin')

@section('title', 'Dashboard - RasaNusantara')

@section('content')
<h1 class="text-xl font-bold text-slate-800 mb-1">Dashboard</h1>
<p class="text-slate-500 text-sm mb-6">Ringkasan aktivitas dapur hari ini, {{ now()->translatedFormat('d F Y') }}</p>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="card p-5">
        <p class="text-slate-500 text-xs mb-1">Total Antrean</p>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['total_antrean'] }}</p>
    </div>
    <div class="card p-5">
        <p class="text-slate-500 text-xs mb-1">Diproses</p>
        <p class="text-2xl font-bold text-amber-600">{{ $stats['diproses'] }}</p>
    </div>
    <div class="card p-5">
        <p class="text-slate-500 text-xs mb-1">Selesai</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $stats['selesai'] }}</p>
    </div>
    <div class="card p-5">
        <p class="text-slate-500 text-xs mb-1">Total Omset</p>
        <p class="text-xl font-bold text-emerald-700">Rp {{ number_format($stats['total_omset'], 0, ',', '.') }}</p>
    </div>
</div>

{{-- Antrean Aktif --}}
<div class="card p-5">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-slate-700">Manajemen Antrean Dapur</h2>
        <span class="bg-emerald-100 text-emerald-700 text-xs font-medium px-3 py-1 rounded-full">
            {{ $activeOrders->count() }} Aktif
        </span>
    </div>

    @if ($activeOrders->isEmpty())
        <p class="text-slate-400 text-sm text-center py-8">Belum ada antrean aktif saat ini.</p>
    @else
        <div class="space-y-3">
            @foreach ($activeOrders as $order)
                <div class="flex items-center justify-between border border-emerald-100 rounded-xl p-4">
                    <div>
                        <p class="font-semibold text-slate-800">#{{ $order->queue_number }} &middot; {{ $order->customer_name }}</p>
                        <p class="text-slate-500 text-xs">{{ $order->table->label }} &middot; {{ $order->items->sum('quantity') }} item &middot; {{ $order->formatted_total }}</p>
                    </div>
                    <span class="text-xs font-medium px-3 py-1 rounded-full
                        {{ $order->status === 'diterima' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600' }}">
                        {{ $order->status_label }}
                    </span>
                </div>
            @endforeach
        </div>
        <a href="{{ route('admin.queue.index') }}" class="btn-secondary w-full text-center block mt-4">
            Kelola Semua Antrean &rarr;
        </a>
    @endif
</div>
@endsection
