@extends('layouts.admin')

@section('title', 'Laporan Omset - RasaNusantara')

@section('content')
<h1 class="text-xl font-bold text-slate-800 mb-1">Laporan Omset</h1>
<p class="text-slate-500 text-sm mb-6">Riwayat transaksi dan ringkasan penjualan</p>

{{-- Filter Tanggal --}}
<form method="GET" class="card p-4 mb-6 flex flex-wrap items-end gap-3">
    <div>
        <label class="text-xs font-medium text-slate-600 mb-1 block">Dari Tanggal</label>
        <input type="date" name="start_date" value="{{ $startDate }}" class="input-field">
    </div>
    <div>
        <label class="text-xs font-medium text-slate-600 mb-1 block">Sampai Tanggal</label>
        <input type="date" name="end_date" value="{{ $endDate }}" class="input-field">
    </div>
    <button type="submit" class="btn-primary">Terapkan Filter</button>
</form>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-slate-500 text-xs mb-1">Total Transaksi</p>
        <p class="text-2xl font-bold text-slate-800">{{ $summary['total_orders'] }}</p>
    </div>
    <div class="card p-5">
        <p class="text-slate-500 text-xs mb-1">Total Omset</p>
        <p class="text-xl font-bold text-emerald-700">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</p>
    </div>
    <div class="card p-5">
        <p class="text-slate-500 text-xs mb-1">Item Terjual</p>
        <p class="text-2xl font-bold text-slate-800">{{ $summary['total_items_sold'] }}</p>
    </div>
</div>

{{-- Menu Terlaris --}}
@if ($summary['best_seller']->isNotEmpty())
<div class="card p-5 mb-6">
    <h2 class="font-semibold text-slate-700 mb-3">Menu Terlaris</h2>
    <div class="space-y-2">
        @foreach ($summary['best_seller'] as $name => $qty)
            <div class="flex justify-between text-sm">
                <span class="text-slate-600">{{ $name }}</span>
                <span class="font-medium text-emerald-700">{{ $qty }}x terjual</span>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Tabel Riwayat --}}
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-emerald-50 text-slate-600 text-left">
            <tr>
                <th class="px-4 py-3">No. Antrean</th>
                <th class="px-4 py-3">Pemesan</th>
                <th class="px-4 py-3">Meja</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Metode Bayar</th>
                <th class="px-4 py-3">Tanggal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-emerald-50">
            @forelse ($orders as $order)
                <tr>
                    <td class="px-4 py-3 font-medium">#{{ $order->queue_number }}</td>
                    <td class="px-4 py-3">{{ $order->customer_name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $order->table->label }}</td>
                    <td class="px-4 py-3 font-medium text-emerald-700">{{ $order->formatted_total }}</td>
                    <td class="px-4 py-3 text-slate-600 capitalize">{{ $order->payment_method }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">Tidak ada transaksi pada rentang tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
