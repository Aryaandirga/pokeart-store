<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Address;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\PosApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class CheckoutController extends Controller
{
    public function __construct(private PosApiService $posApi)
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    public function index()
    {
        $cart = Cart::where('user_id', auth()->id())
            ->with('items')
            ->firstOrFail();

        $allProducts = $this->posApi->getProducts(['per_page' => 100]);
        $productsMap = collect($allProducts['data'] ?? [])->keyBy('id');

        $validItems = $cart->items->filter(function ($item) use ($productsMap) {
            $product = $productsMap->get($item->product_id);

            return !empty($product) && ($product['is_active'] ?? true);
        })->values();

        $invalidItemIds = $cart->items
            ->whereNotIn('id', $validItems->pluck('id')->all())
            ->pluck('id')
            ->all();

        if ($invalidItemIds !== []) {
            \App\Models\CartItem::whereIn('id', $invalidItemIds)->delete();
        }

        if ($validItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kamu kosong.');
        }

        $checkoutItems = $validItems->map(function ($item) use ($productsMap) {
            $product = $productsMap->get($item->product_id);

            return [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'product' => $product,
            ];
        });

        $subtotal = $checkoutItems->sum(
            fn ($item) => ($item['product']['price'] ?? 0) * $item['quantity']
        );
        $addresses = Address::where('user_id', auth()->id())->get();

        return Inertia::render('Checkout', [
            'cart' => [
                'id' => $cart->id,
                'items' => $checkoutItems,
            ],
            'subtotal' => $subtotal,
            'addresses' => $addresses,
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'address_id'     => 'nullable|exists:addresses,id',
            'recipient_name' => 'required_without:address_id|string|max:100',
            'phone'          => 'required_without:address_id|string|max:20',
            'address'        => 'required_without:address_id|string',
            'city'           => 'required_without:address_id|string|max:100',
            'province'       => 'required_without:address_id|string|max:100',
            'postal_code'    => 'required_without:address_id|string|max:10',
            'shipping_cost'  => 'required|numeric|min:0',
            'notes'          => 'nullable|string|max:500',
        ]);

        $cart  = Cart::where('user_id', auth()->id())->firstOrFail();
        $items = \App\Models\CartItem::where('cart_id', $cart->id)->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Keranjang kosong.');
        }

        // Ambil produk dari POS API
        $allProducts = $this->posApi->getProducts(['per_page' => 100]);
        $productsMap = collect($allProducts['data'] ?? [])->keyBy('id');

        // Simpan alamat baru jika tidak pilih yang existing
        if (!$request->address_id) {
            $addressModel = Address::create([
                'user_id'        => auth()->id(),
                'label'          => 'Alamat Pengiriman',
                'recipient_name' => $request->recipient_name,
                'phone'          => $request->phone,
                'address'        => $request->address,
                'city'           => $request->city,
                'province'       => $request->province,
                'postal_code'    => $request->postal_code,
                'is_default'     => false,
            ]);
            $addressId = $addressModel->id;
        } else {
            $addressModel = Address::find($request->address_id);
            $addressId    = $request->address_id;
        }

        $subtotal = $items->sum(function ($item) use ($productsMap) {
            $product = $productsMap->get($item->product_id);
            return ($product['price'] ?? 0) * $item->quantity;
        });
        $shippingCost = $request->shipping_cost;
        $total        = $subtotal + $shippingCost;

        $order = Order::create([
            'user_id'       => auth()->id(),
            'address_id'    => $addressId,
            'order_number'  => 'ORD-' . strtoupper(Str::random(10)),
            'status'        => 'pending',
            'subtotal'      => $subtotal,
            'shipping_cost' => $shippingCost,
            'total'         => $total,
            'notes'         => $request->notes,
        ]);

        foreach ($items as $item) {
            $product = $productsMap->get($item->product_id);
            $price   = $product['price'] ?? 0;
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $price,
                'subtotal'   => $price * $item->quantity,
            ]);
        }

        Payment::create([
            'order_id' => $order->id,
            'amount'   => $total,
            'status'   => 'pending',
        ]);

        $user = auth()->user();

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) $total,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
                'phone'      => $addressModel->phone ?? '',
                'shipping_address' => [
                    'first_name'  => $addressModel->recipient_name ?? $user->name,
                    'address'     => $addressModel->address ?? '',
                    'city'        => $addressModel->city ?? '',
                    'postal_code' => $addressModel->postal_code ?? '',
                    'phone'       => $addressModel->phone ?? '',
                ],
            ],
            'item_details' => $items->map(function ($item) use ($productsMap) {
                $product = $productsMap->get($item->product_id);
                return [
                    'id'       => (string) $item->product_id,
                    'price'    => (int) ($product['price'] ?? 0),
                    'quantity' => $item->quantity,
                    'name'     => substr($product['name'] ?? 'Produk', 0, 50),
                ];
            })->toArray(),
        ];

        if ($shippingCost > 0) {
            $params['item_details'][] = [
                'id'       => 'SHIPPING',
                'price'    => (int) $shippingCost,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim',
            ];
        }

        $snapToken = Snap::getSnapToken($params);

        // Kosongkan cart
        \App\Models\CartItem::where('cart_id', $cart->id)->delete();

        return response()->json([
            'snap_token'   => $snapToken,
            'order_number' => $order->order_number,
        ]);
    }

    public function callback(Request $request)
    {
        $notification = new Notification();

        $orderId           = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        $paymentType       = $notification->payment_type;
        $transactionId     = $notification->transaction_id;
        $fraudStatus       = $notification->fraud_status;

        $order = Order::where('order_number', $orderId)->first();
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $payment = $order->payment;

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'accept' || $fraudStatus === null) {
                $order->update(['status' => 'paid']);
                $payment?->update([
                    'status'         => 'success',
                    'transaction_id' => $transactionId,
                    'payment_method' => $paymentType,
                ]);
                $this->syncToSales($order, $paymentType);
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $order->update(['status' => 'cancelled']);
            $payment?->update(['status' => 'failed']);
        } elseif ($transactionStatus === 'pending') {
            $order->update(['status' => 'pending']);
            $payment?->update(['status' => 'pending']);
        }

        return response()->json(['message' => 'OK']);
    }

    public function syncSale(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);

        $order = Order::where('order_number', $request->order_number)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $order->update(['status' => 'paid']);
        $order->payment?->update(['status' => 'success']);

        $this->syncToSales($order, 'transfer');

        return response()->json(['message' => 'OK']);
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);

        return Inertia::render('CheckoutSuccess', [
            'order' => $order->load(['items.product', 'address', 'payment']),
        ]);
    }

    private function syncToSales(Order $order, ?string $paymentType): void
    {
        if (Sale::where('invoice_no', $order->order_number)->exists()) return;

        $order->loadMissing('items.product');

        $paymentMap = [
            'credit_card'   => 'transfer',
            'bank_transfer' => 'transfer',
            'echannel'      => 'transfer',
            'bni_va'        => 'transfer',
            'bri_va'        => 'transfer',
            'bca_va'        => 'transfer',
            'other_va'      => 'transfer',
            'transfer'      => 'transfer',
            'gopay'         => 'qris',
            'shopeepay'     => 'qris',
            'qris'          => 'qris',
            'cash'          => 'cash',
            'online'        => 'transfer',
        ];

        $sale = Sale::create([
            'user_id'        => $order->user_id,
            'invoice_no'     => $order->order_number,
            'date'           => $order->created_at->toDateString(),
            'total'          => $order->subtotal,
            'discount'       => 0,
            'grand_total'    => $order->total,
            'payment_method' => $paymentMap[$paymentType] ?? 'transfer',
            'amount_paid'    => $order->total,
            'change'         => 0,
            'status'         => 'completed',
            'notes'          => 'Toko Online' . ($order->notes ? ' - ' . $order->notes : ''),
        ]);

        foreach ($order->items as $item) {
            SaleItem::create([
                'sale_id'    => $sale->id,
                'product_id' => $item->product_id,
                'qty'        => $item->quantity,
                'price'      => $item->price,
                'subtotal'   => $item->subtotal,
            ]);
        }

        // Kirim ke POS API — catat transaksi & kurangi stok
        $this->posApi->createOrder([
            'grand_total'   => $order->total,
            'customer_name' => auth()->user()?->name ?? 'Guest',
            'items'         => $order->items->map(fn($item) => [
                'id'    => $item->product_id,
                'qty'   => $item->quantity,
                'price' => $item->price,
            ])->toArray(),
        ]);
    }
}
