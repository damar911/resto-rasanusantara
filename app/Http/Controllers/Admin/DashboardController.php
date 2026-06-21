<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard utama: statistik total antrean, diproses, selesai, dan omset hari ini.
     */
    public function index(): View
    {
        $today = today();

        $stats = [
            'total_antrean' => Order::whereDate('created_at', $today)->count(),
            'diproses' => Order::whereIn('status', ['diterima', 'dimasak'])
                ->whereDate('created_at', $today)
                ->count(),
            'selesai' => Order::where('status', 'disajikan')
                ->whereDate('created_at', $today)
                ->count(),
            'total_omset' => Order::where('status', 'disajikan')
                ->whereDate('created_at', $today)
                ->sum('total_amount'),
        ];

        $activeOrders = Order::with('items', 'table')
            ->active()
            ->orderBy('created_at')
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'activeOrders' => $activeOrders,
        ]);
    }
}
