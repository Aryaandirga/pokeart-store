<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'payment', 'address'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('order_number', 'ilike', '%' . $request->search . '%')
                ->orWhereHas('user', fn($q) =>
                    $q->where('name', 'ilike', '%' . $request->search . '%')
                );
        }

        $orders = $query->paginate(15)->withQueryString();

        return Inertia::render('admin/Orders', [
            'orders'  => $orders,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', "Status order #{$order->order_number} diupdate.");
    }
}