<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuManagementController extends Controller
{
    /**
     * Tampilkan katalog menu dapur, bisa difilter per kategori.
     */
    public function index(): View
    {
        $menus = Menu::with('category')->orderBy('category_id')->orderBy('name')->get();
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.menus.index', [
            'menus' => $menus,
            'categories' => $categories,
        ]);
    }

    /**
     * Simpan menu baru (dipanggil dari modal "Tambah Menu").
     */
    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('menus', 'public');
        }

        Menu::create($validated);

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Update menu yang sudah ada.
     */
    public function update(StoreMenuRequest $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            if ($menu->photo) {
                Storage::disk('public')->delete($menu->photo);
            }
            $validated['photo'] = $request->file('photo')->store('menus', 'public');
        }

        $menu->update($validated);

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Hapus menu beserta foto terkait.
     */
    public function destroy(Menu $menu): RedirectResponse
    {
        if ($menu->photo) {
            Storage::disk('public')->delete($menu->photo);
        }

        $menu->delete();

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }
}
