<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products'   => Product::where('is_active', true)->count(),
            'published'        => Product::where('is_published', true)->count(),
            'total_orders'     => Order::count(),
            'pending_orders'   => Order::where('status', 'pending')->count(),
            'paid_orders'      => Order::where('status', 'paid')->count(),
            'total_revenue'    => Order::whereIn('status', ['paid', 'delivered'])->sum('total'),
        ];

        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(5)
            ->get();

        $lowStock = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->take(5)
            ->get();

        return Inertia::render('admin/Dashboard', [
            'stats'        => $stats,
            'recentOrders' => $recentOrders,
            'lowStock'     => $lowStock,
        ]);
    }
}