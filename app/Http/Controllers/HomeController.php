<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with(['category', 'images'])
            ->where('is_published', true)
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::withCount('products')->get();

        return Inertia::render('Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
        ]);
    }
}