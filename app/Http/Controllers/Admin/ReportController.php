<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Laporan omset & riwayat pesanan, hanya untuk Admin Dapur.
     * Bisa difilter berdasarkan rentang tanggal.
     */
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', today()->toDateString());

        $orders = Order::with('items', 'table')
            ->whereBetween('created_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"])
            ->where('status', 'disajikan')
            ->orderByDesc('created_at')
            ->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_amount'),
            'total_items_sold' => $orders->flatMap->items->sum('quantity'),
            'best_seller' => $orders->flatMap->items
                ->groupBy('menu_name')
                ->map(fn ($items) => $items->sum('quantity'))
                ->sortDesc()
                ->take(5),
        ];

        return view('admin.reports.index', [
            'orders' => $orders,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
