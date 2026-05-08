<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // Kategori dengan jumlah produk yang published & aktif
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('is_published', true)->where('is_active', true);
        }])->get();

        // Produk unggulan: ambil berdasarkan terlaris dari sale_items
        // Fallback ke produk terbaru jika belum ada data penjualan
        $topProductIds = DB::table('sale_items')
            ->select('product_id', DB::raw('SUM(qty) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(8)
            ->pluck('product_id')
            ->toArray();

        if (!empty($topProductIds)) {
            // Ambil produk terlaris yang masih published
            $featuredProducts = Product::with(['category', 'images'])
                ->where('is_published', true)
                ->where('is_active', true)
                ->whereIn('id', $topProductIds)
                ->get()
                ->sortBy(fn($p) => array_search($p->id, $topProductIds))
                ->values();

            // Kalau kurang dari 8, tambah dari produk terbaru
            if ($featuredProducts->count() < 8) {
                $existing = $featuredProducts->pluck('id')->toArray();
                $extra = Product::with(['category', 'images'])
                    ->where('is_published', true)
                    ->where('is_active', true)
                    ->whereNotIn('id', $existing)
                    ->latest()
                    ->take(8 - $featuredProducts->count())
                    ->get();
                $featuredProducts = $featuredProducts->concat($extra);
            }
        } else {
            // Belum ada data penjualan, tampilkan produk terbaru
            $featuredProducts = Product::with(['category', 'images'])
                ->where('is_published', true)
                ->where('is_active', true)
                ->latest()
                ->take(8)
                ->get();
        }

        return Inertia::render('Home', [
            'featuredProducts' => $featuredProducts,
            'categories'       => $categories,
        ]);
    }
}
