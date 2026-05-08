<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Services\PosApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function __construct(private PosApiService $posApi) {}

    public function index()
    {
        $wishlistIds = Wishlist::where('user_id', auth()->id())
            ->pluck('product_id')
            ->toArray();

        // Ambil detail produk dari POS API
        $wishlists = [];
        foreach ($wishlistIds as $productId) {
            $product = $this->posApi->getProductById($productId);
            if ($product) {
                $wishlists[] = [
                    'id'      => $productId,
                    'product' => $product,
                ];
            }
        }

        return Inertia::render('Wishlist', [
            'wishlists' => $wishlists,
        ]);
    }

    public function toggle(Request $request, $productId)
    {
        \Log::info('Wishlist toggle called', [
            'user_id'    => auth()->id(),
            'product_id' => $productId,
            'auth_check' => auth()->check(),
        ]);

        if (!auth()->check()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $action = 'removed';
        } else {
            Wishlist::create([
                'user_id'    => auth()->id(),
                'product_id' => $productId,
            ]);
            $action = 'added';
        }

        \Log::info('Wishlist toggle done', ['action' => $action, 'product_id' => $productId]);

        return response()->json([
            'action'     => $action,
            'product_id' => $productId,
        ]);
    }
}
