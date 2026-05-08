<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Services\PosApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __construct(private PosApiService $posApi) {}

    private function getOrCreateCart()
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    public function index()
    {
        $cart  = $this->getOrCreateCart();
        $items = CartItem::where('cart_id', $cart->id)->get();

        // Ambil semua produk dari POS API sekaligus
        $allProducts = $this->posApi->getProducts(['per_page' => 100]);
        $productsMap = collect($allProducts['data'] ?? [])->keyBy('id');

        $cartItems = $items->map(function ($item) use ($productsMap) {
            $product = $productsMap->get($item->product_id);
            return [
                'id'       => $item->id,
                'quantity' => $item->quantity,
                'product'  => $product ?? ['id' => $item->product_id, 'name' => 'Produk tidak tersedia', 'price' => 0],
            ];
        });

        $subtotal = $cartItems->sum(fn($item) =>
            ($item['product']['price'] ?? 0) * $item['quantity']
        );

        return Inertia::render('Cart', [
            'cart'     => ['items' => $cartItems],
            'subtotal' => $subtotal,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity'   => 'required|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart();
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($item) {
            $item->update(['quantity' => $item->quantity + $request->quantity]);
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Produk ditambahkan ke keranjang!']);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        // Pastikan cart milik user ini
        if ($cartItem->cart->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate(['quantity' => 'required|integer|min:1']);

        $product = $this->posApi->getProductById($cartItem->product_id);
        $stock   = $product['stock'] ?? 999;

        if ($request->quantity > $stock) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cartItem->update(['quantity' => $request->quantity]);
        return back();
    }

    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            abort(403);
        }
        $cartItem->delete();
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
