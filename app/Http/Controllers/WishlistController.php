<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
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
public function toggle(Product $product)
{
    \Log::info('Wishlist toggle', [
        'user_id'    => auth()->id(),
        'product_id' => $product->id,
    ]);

    $existing = Wishlist::where('user_id', auth()->id())
        ->where('product_id', $product->id)
        ->first();

    if ($existing) {
        $existing->delete();
    } else {
        Wishlist::create([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
        ]);
    }

    return redirect()->back();
}
}