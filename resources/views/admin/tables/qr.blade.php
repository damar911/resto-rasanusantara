<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Meja - RasaNusantara</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; padding: 24px; background: #f0fdf4; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        p.note { color: #64748b; font-size: 13px; margin: 0 0 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
        .card { background: #fff; border: 1px solid #a7f3d0; border-radius: 16px; padding: 20px; text-align: center; break-inside: avoid; }
        .card h2 { margin: 0 0 4px; color: #047857; font-size: 20px; }
        .card small { color: #64748b; display: block; margin-bottom: 12px; }
        .qr { display: flex; justify-content: center; }
        button { margin-bottom: 16px; padding: 10px 18px; border: 0; border-radius: 12px; background: #059669; color: #fff; font-weight: 600; cursor: pointer; }
        @media print { button, .note { display: none; } body { background: #fff; padding: 0; } }
    </style>
</head>
<body>
    <h1>QR Code Meja - RasaNusantara</h1>
    <p class="note">Cetak halaman ini, lalu tempel QR di masing-masing meja. Pelanggan yang scan langsung masuk ke menu dengan meja terpilih.</p>
    <button onclick="window.print()">Cetak</button>

    <div class="grid">
        @foreach ($tables as $table)
            <div class="card">
                <h2>{{ $table->label }}</h2>
                <small>Scan untuk memesan</small>
                <div class="qr" data-url="{{ route('customer.menu', ['meja' => $table->number]) }}"></div>
            </div>
        @endforeach
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.querySelectorAll('.qr').forEach(el => {
            new QRCode(el, { text: el.dataset.url, width: 160, height: 160 });
        });
    </script>
</body>
</html>
