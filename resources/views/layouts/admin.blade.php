<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Admin Dapur - RasaNusantara')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-emerald-50 min-h-screen font-sans antialiased text-slate-800">

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="hidden md:flex md:w-60 bg-emerald-700 text-white flex-col">
            <div class="p-5 border-b border-emerald-600">
                <h1 class="font-bold text-lg">RasaNusantara</h1>
                <p class="text-emerald-200 text-xs">Portal Admin Dapur</p>
            </div>

            <nav class="flex-1 p-4 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 font-medium' : 'hover:bg-emerald-600/60' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.queue.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.queue.*') ? 'bg-emerald-600 font-medium' : 'hover:bg-emerald-600/60' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Manajemen Antrean
                </a>

                @if (auth()->user()?->isAdminDapur())
                    <a href="{{ route('admin.menus.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.menus.*') ? 'bg-emerald-600 font-medium' : 'hover:bg-emerald-600/60' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Katalog Menu
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.reports.*') ? 'bg-emerald-600 font-medium' : 'hover:bg-emerald-600/60' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Laporan Omset
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-emerald-600">
                <p class="text-sm font-medium">{{ auth()->user()?->name }}</p>
                <p class="text-emerald-200 text-xs mb-3">{{ auth()->user()?->isAdminDapur() ? 'Admin Dapur' : 'Operator' }}</p>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-emerald-200 hover:text-white flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Mobile top bar --}}
        <div class="md:hidden fixed top-0 inset-x-0 bg-emerald-700 text-white px-4 py-3 flex items-center justify-between z-40">
            <span class="font-bold">RasaNusantara Admin</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-emerald-200">Keluar</button>
            </form>
        </div>

        {{-- Main Content --}}
        <main class="flex-1 p-4 md:p-8 mt-14 md:mt-0">
            @if (session('success'))
                <div class="bg-emerald-100 text-emerald-800 rounded-xl px-4 py-3 mb-5 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
