<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Inertia\Inertia;

class MyOrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return Inertia::render('MyOrders', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);

        return Inertia::render('MyOrderDetail', [
            'order' => $order->load(['items.product', 'address', 'payment']),
        ]);
    }
}