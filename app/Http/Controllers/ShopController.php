<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images'])
            ->where('is_published', true)
            ->where('is_active', true);

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->search) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        match($request->sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::withCount(['products' => function($q) {
            $q->where('is_published', true)->where('is_active', true);
        }])->get();

        $wishlistedIds = [];
        if (auth()->check()) {
            $wishlistedIds = Wishlist::where('user_id', auth()->id())
                ->pluck('product_id')
                ->toArray();
        }

        return Inertia::render('Shop', [
            'products'      => $products,
            'categories'    => $categories,
            'filters'       => $request->only(['search', 'category', 'sort']),
            'wishlistedIds' => $wishlistedIds,
        ]);
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'images'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_published', true)
            ->take(10)
            ->get();

        $isWishlisted = false;
        if (auth()->check()) {
            $isWishlisted = Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists();
        }

        return Inertia::render('ShopDetail', [
            'product'  => array_merge($product->toArray(), ['is_wishlisted' => $isWishlisted]),
            'related'  => $related,
        ]);
    }
}