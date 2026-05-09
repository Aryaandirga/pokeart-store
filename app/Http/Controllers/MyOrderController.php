<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PosApiService;
use Inertia\Inertia;

class MyOrderController extends Controller
{
    public function __construct(private PosApiService $posApi) {}

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items', 'payment'])
            ->latest()
            ->paginate(10);

        // Ambil semua product_id dari semua order items
        $productIds = $orders->flatMap(fn($o) => $o->items->pluck('product_id'))->unique()->values();

        // Ambil produk dari POS API
        $allProducts = $this->posApi->getProducts(['per_page' => 100]);
        $productsMap = collect($allProducts['data'] ?? [])->keyBy('id');

        // Map produk ke setiap order item
        $orders->getCollection()->transform(function ($order) use ($productsMap) {
            $order->items->transform(function ($item) use ($productsMap) {
                $item->product = $productsMap->get($item->product_id) ?? [
                    'id'    => $item->product_id,
                    'name'  => 'Produk tidak tersedia',
                    'price' => $item->price,
                    'image' => null,
                ];
                return $item;
            });
            return $order;
        });

        return Inertia::render('MyOrders', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);

        $order->load(['items', 'address', 'payment']);

        $allProducts = $this->posApi->getProducts(['per_page' => 100]);
        $productsMap = collect($allProducts['data'] ?? [])->keyBy('id');

        $order->items->transform(function ($item) use ($productsMap) {
            $item->product = $productsMap->get($item->product_id) ?? [
                'id'    => $item->product_id,
                'name'  => 'Produk tidak tersedia',
                'price' => $item->price,
                'image' => null,
            ];
            return $item;
        });

        return Inertia::render('MyOrderDetail', [
            'order' => $order,
        ]);
    }
}