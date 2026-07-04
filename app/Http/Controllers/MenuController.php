<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\RestaurantTable;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('sort_order')->get();

        $menus = Menu::with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get()
            ->groupBy('category_id');

        $tables = RestaurantTable::active()->orderBy('number')->get();

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
            'categories' => $categories,
            'menus'      => $menus,
            'tables'     => $tables,
            'menusForJs' => $menusForJs,
        ]);
    }
}
