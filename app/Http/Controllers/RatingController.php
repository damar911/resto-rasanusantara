<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuRating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Simpan rating baru dari customer.
     * Setiap submit selalu dihitung sebagai ulasan baru (tidak dibatasi per session),
     * supaya rating_count benar-benar merefleksikan jumlah orang yang menilai menu ini.
     */
    public function store(Request $request, Menu $menu): JsonResponse
    {
        $validated = $request->validate([
            'rating'        => ['required', 'integer', 'min:1', 'max:5'],
            'reviewer_name' => ['nullable', 'string', 'max:80'],
        ]);

        MenuRating::create([
            'menu_id'       => $menu->id,
            'rating'        => $validated['rating'],
            'reviewer_name' => $validated['reviewer_name'] ?? null,
            // session_id tetap disimpan untuk keperluan analitik/log,
            // tapi tidak lagi dipakai untuk membatasi jumlah rating.
            'session_id'    => $request->session()->getId(),
        ]);

        $menu->recalculateRating();

        return response()->json([
            'success'      => true,
            'avg_rating'   => round($menu->avg_rating, 1),
            'rating_count' => $menu->rating_count,
        ]);
    }
}