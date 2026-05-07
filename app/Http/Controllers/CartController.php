<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    private function getOrCreateCart()
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.product.category', 'items.product.images']);

        $subtotal = $cart->items->sum(fn($item) =>
            $item->product->price * $item->quantity
        );

        return Inertia::render('Cart', [
            'cart'     => $cart,
            'subtotal' => $subtotal,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cart = $this->getOrCreateCart();

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $newQty = $item->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return back()->with('error', 'Stok tidak mencukupi.');
            }
            $item->update(['quantity' => $newQty]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
            ]);
        }

        return back()->with('success', 'Produk ditambahkan ke keranjang!');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorize('update', $cartItem->cart);

        $request->validate(['quantity' => 'required|integer|min:1']);

        if ($request->quantity > $cartItem->product->stock) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back();
    }

    public function destroy(CartItem $cartItem)
    {
        $this->authorize('update', $cartItem->cart);
        $cartItem->delete();
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}