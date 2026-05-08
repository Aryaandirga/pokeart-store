<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Services\PosApiService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(private PosApiService $posApi) {}

    public function index(Request $request)
    {
        $params = array_filter([
            'search'   => $request->search,
            'category' => $request->category,
            'sort'     => $request->sort,
            'per_page' => 12,
            'page'     => $request->page ?? 1,
        ]);

        $productsData = $this->posApi->getProducts($params);
        $categories   = $this->posApi->getCategories();

        $wishlistedIds = [];
        if (auth()->check()) {
            $wishlistedIds = Wishlist::where('user_id', auth()->id())
                ->pluck('product_id')
                ->toArray();
        }

        return Inertia::render('Shop', [
            'products'      => $productsData,
            'categories'    => $categories,
            'filters'       => $request->only(['search', 'category', 'sort']),
            'wishlistedIds' => $wishlistedIds,
        ]);
    }

    public function show($slug)
    {
        $product = $this->posApi->getProduct($slug);

        if (!$product) abort(404);

        $related = $this->posApi->getProducts([
            'category' => $product['category']['slug'] ?? null,
            'per_page' => 10,
        ]);

        $isWishlisted = false;
        if (auth()->check()) {
            $isWishlisted = Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product['id'])
                ->exists();
        }

        return Inertia::render('ShopDetail', [
            'product' => array_merge($product, ['is_wishlisted' => $isWishlisted]),
            'related' => $related['data'] ?? [],
        ]);
    }
}