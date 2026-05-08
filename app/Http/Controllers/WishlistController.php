<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('user_id', auth()->id())
            ->with(['product.category', 'product.images'])
            ->latest()
            ->get();

        return Inertia::render('Wishlist', [
            'wishlists' => $wishlists,
        ]);
    }

    public function toggle(Request $request, $productId)
    {
        // Cari product tanpa Route Model Binding
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['error' => 'Product not found', 'id' => $productId], 404);
        }

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $action = 'removed';
        } else {
            Wishlist::create([
                'user_id'    => auth()->id(),
                'product_id' => $product->id,
            ]);
            $action = 'added';
        }

        return response()->json(['action' => $action, 'product_id' => $product->id]);
    }
}
