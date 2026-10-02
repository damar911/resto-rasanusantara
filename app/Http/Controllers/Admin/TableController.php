<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TableController extends Controller
{
    /**
     * Halaman manajemen meja (kosong / terisi).
     */
    public function index(): View
    {
        return view('admin.tables.index', [
            'tables' => $this->tableData(),
        ]);
    }

    /**
     * Data status meja (dipanggil berkala oleh halaman admin).
     */
    public function status(): JsonResponse
    {
        return response()->json($this->tableData());
    }

    public function occupy(RestaurantTable $table): JsonResponse
    {
        // Tandai manual (mis. pelanggan datang tanpa scan). Tanpa nomor WA,
        // sehingga tidak ada pelanggan yang bisa memesan ke meja ini.
        $table->occupy('Ditandai admin', null);

        return response()->json(['success' => true]);
    }

    public function free(RestaurantTable $table): JsonResponse
    {
        $table->free();

        return response()->json(['success' => true]);
    }

    /**
     * Halaman cetak QR code per meja.
     */
    public function qr(): View
    {
        return view('admin.tables.qr', [
            'tables' => RestaurantTable::active()->orderBy('number')->get(),
        ]);
    }

    protected function tableData()
    {
        return RestaurantTable::active()
            ->orderBy('number')
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'label' => $t->label,
                'is_occupied' => $t->is_occupied,
                'occupant_name' => $t->occupant_name,
                'occupied_since' => $t->occupied_at?->format('H:i'),
            ])
            ->values();
    }
}
