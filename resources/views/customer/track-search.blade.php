@extends('layouts.customer')

@section('title', 'Lacak Antrean - RasaNusantara')

@section('content')
<div class="card p-5">
    <h2 class="text-lg font-bold text-slate-800 mb-1">Lacak Status Antrean</h2>
    <p class="text-sm text-slate-500 mb-4">
        Masukkan nomor antrean Anda yang tertera pada nota atau notifikasi pesan WhatsApp.
    </p>

    <form method="POST" action="{{ route('order.track-search.submit') }}" class="flex flex-col sm:flex-row gap-3">
        @csrf
        <input
            type="text"
            name="queue_number"
            value="{{ old('queue_number') }}"
            placeholder="Contoh: 01"
            class="flex-1 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
        >
        <button type="submit" class="btn-primary px-6 py-3 text-sm font-semibold">
            Cari
        </button>
    </form>

    @error('queue_number')
        <p class="text-red-600 text-sm mt-3">{{ $message }}</p>
    @enderror
</div>
@endsection