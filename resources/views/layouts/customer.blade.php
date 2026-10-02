<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RasaNusantara - Sistem Pemesanan & Antrean Digital')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-emerald-50 min-h-screen pb-20 font-sans antialiased text-slate-800">

    <header class="bg-emerald-600 text-white sticky top-0 z-40 shadow-md">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <div>
                <h1 class="font-bold text-lg leading-tight">RasaNusantara</h1>
                <p class="text-emerald-100 text-xs">Masakan Nusantara</p>
            </div>
            <span class="bg-emerald-700 text-emerald-50 text-xs px-3 py-1 rounded-full">
                Meja Pemesanan Mandiri
            </span>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    {{-- Bottom Navigation - 3 item, tanpa Admin --}}
    <nav class="fixed bottom-0 inset-x-0 bg-white border-t border-emerald-100 shadow-lg z-40">
        <div class="max-w-5xl mx-auto grid grid-cols-3 text-center text-xs">

            <a href="{{ route('customer.menu') }}"
   class="flex flex-col items-center gap-1 py-3
   {{ request()->routeIs('customer.menu') ? 'text-emerald-700' : 'text-slate-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span>Semua Menu</span>
            </a>

            <button type="button" id="nav-cart-btn"
                class="flex flex-col items-center gap-1 py-3 text-slate-500 relative">
                <span class="relative inline-flex">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span id="cart-badge"
    style="display:none;"
    class="absolute -top-2 -right-2 bg-orange-500 text-white text-[10px] min-w-[18px] h-[18px] px-1 rounded-full items-center justify-center font-bold leading-none">
    0
</span>
                </span>
                <span>Keranjang</span>
            </button>

            <a href="{{ route('order.track-search') }}"
   class="flex flex-col items-center gap-1 py-3
   {{ request()->routeIs('order.track-search') ? 'text-emerald-700' : 'text-slate-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Lacak Antrean</span>
            </a>

        </div>
    </nav>

    @yield('modals')

    <footer class="max-w-5xl mx-auto px-4 py-4 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} RasaNusantara Resto.
    </footer>

    @stack('scripts')
</body>
</html>
