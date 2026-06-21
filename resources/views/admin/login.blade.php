<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - RasaNusantara</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-emerald-50 min-h-screen flex items-center justify-center p-4 font-sans antialiased">

    <div class="w-full max-w-sm">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-emerald-700">RasaNusantara</h1>
            <p class="text-slate-500 text-sm">Portal Admin Dapur</p>
        </div>

        <div class="card p-6">
            <p class="text-slate-500 text-sm mb-4 text-center">
                Gunakan Akun Demo Sandbox atau login menggunakan kredensial terdaftar.
            </p>

            @if ($errors->any())
                <div class="bg-red-50 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs font-medium text-slate-600 mb-1 block">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input-field" placeholder="admin@resto.test">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-600 mb-1 block">Password</label>
                    <input type="password" name="password" required class="input-field" placeholder="••••••••">
                </div>
                <button type="submit" class="btn-primary w-full mt-2">Masuk Sekarang</button>
            </form>

            <div class="mt-5 pt-4 border-t border-emerald-100">
                <p class="text-xs text-slate-400 mb-2 text-center">Akun Uji Coba Cepat:</p>
                <div class="grid grid-cols-2 gap-2">
                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf
                        <input type="hidden" name="email" value="admindapur@resto.test">
                        <input type="hidden" name="password" value="password">
                        <button type="submit" class="btn-secondary w-full text-xs py-2">Admin Dapur</button>
                    </form>
                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf
                        <input type="hidden" name="email" value="operator@resto.test">
                        <input type="hidden" name="password" value="password">
                        <button type="submit" class="btn-secondary w-full text-xs py-2">Operator</button>
                    </form>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-4">
            <a href="{{ route('customer.menu') }}" class="hover:text-emerald-600">&larr; Kembali ke halaman menu</a>
        </p>
    </div>

</body>
</html>
