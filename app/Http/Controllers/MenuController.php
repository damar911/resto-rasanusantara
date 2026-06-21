<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\RestaurantTable;
use Illuminate\View\View;

class MenuController extends Controller
{
    /**
     * Halaman utama customer: hero section + filter kategori + daftar menu.
     */
    public function index(): View
    {
        $categories = Category::orderBy('sort_order')->get();

        $menus = Menu::with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get()
            ->groupBy('category_id');

        $tables = RestaurantTable::active()->orderBy('number')->get();

        return view('customer.menu', [
            'categories' => $categories,
            'menus' => $menus,
            'tables' => $tables,
        ]);
    }
}
