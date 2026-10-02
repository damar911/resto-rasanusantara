<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\RestaurantTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('sort_order')->get();

        $menus = Menu::with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get()
            ->groupBy('category_id');

        $tables = $this->tableOptions();

        // Scan QR meja: /?meja=5 -> meja otomatis terpilih (jika masih kosong)
        $preselectedTableId = '';
        if ($request->filled('meja')) {
            $preselected = RestaurantTable::active()
                ->where('number', (int) $request->query('meja'))
                ->first();

            if ($preselected && $preselected->isAvailableFor(null)) {
                $preselectedTableId = $preselected->id;
            }
        }

        // Data flat untuk search/filter di JavaScript — disiapkan di controller
        // agar tidak ada konflik sintaks Blade di dalam @json()
        $menusForJs = $menus->flatten()->map(function ($m) {
            return [
                'id'          => $m->id,
                'category_id' => $m->category_id,
                'name'        => $m->name,
                'description' => $m->description ?? '',
            ];
        })->values();

        return view('customer.menu', [
            'categories'         => $categories,
            'menus'              => $menus,
            'tables'             => $tables,
            'menusForJs'         => $menusForJs,
            'preselectedTableId' => $preselectedTableId,
        ]);
    }

    /**
     * Ketersediaan meja (dipanggil dari keranjang). Jika nomor WA dikirim,
     * meja yang sedang dipakai pelanggan yang sama tetap tersedia.
     */
    public function tables(Request $request): JsonResponse
    {
        return response()->json($this->tableOptions($request->query('whatsapp')));
    }

    protected function tableOptions(?string $whatsapp = null): Collection
    {
        return RestaurantTable::active()
            ->orderBy('number')
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'label' => $t->label,
                'available' => $t->isAvailableFor($whatsapp),
            ])
            ->values();
    }
}
