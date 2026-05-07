<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TrackingController extends Controller
{
    public function index()
    {
        // Top produk terlaris dari sale_items (data POS)
        $topSelling = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'products.slug',
                DB::raw('SUM(sale_items.qty) as total_sold'),
                DB::raw('AVG(sale_items.price) as avg_price'),
                DB::raw('MAX(sale_items.price) as max_price'),
                DB::raw('MIN(sale_items.price) as min_price'),
            )
            ->groupBy('products.id', 'products.name', 'products.slug')
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();

        // Tren penjualan 30 hari terakhir (per hari)
        $salesTrend = DB::table('sales')
            ->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("COUNT(*) as total_orders"),
                DB::raw("SUM(grand_total) as total_revenue"),
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy('date')
            ->get();

        // Total statistik
        $stats = [
            'total_products' => DB::table('products')
                ->where('is_active', true)->count(),
            'total_sold'     => DB::table('sale_items')
                ->sum('qty'),
            'avg_price'      => DB::table('products')
                ->where('is_active', true)->avg('price'),
            'total_orders'   => DB::table('sales')->count(),
        ];

        return Inertia::render('Tracking', [
            'topSelling' => $topSelling,
            'salesTrend' => $salesTrend,
            'stats'      => $stats,
        ]);
    }
}