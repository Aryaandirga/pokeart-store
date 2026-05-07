<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    public function searchArea(Request $request)
    {
        $request->validate(['q' => 'required|string|min:3']);

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . config('biteship.api_key'),
            ])->get(config('biteship.base_url') . '/v1/maps/areas', [
                'countries' => 'ID',
                'input'     => $request->q,
                'type'      => 'single',
            ]);

            Log::info('Biteship area search', [
                'q'      => $request->q,
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 300),
            ]);

            if ($response->failed()) {
                return response()->json(['areas' => [], 'error' => $response->json('error') ?? 'Gagal mencari area']);
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            Log::error('Biteship area search exception: ' . $e->getMessage());
            return response()->json(['areas' => [], 'error' => $e->getMessage()]);
        }
    }

    public function getRates(Request $request)
    {
        $request->validate([
            'destination_area_id' => 'required|string',
        ]);

        $cart = Cart::where('user_id', auth()->id())
            ->with('items.product')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Keranjang kosong'], 400);
        }

        $totalWeight = max($cart->items->sum(function ($item) {
            return ($item->product->weight ?? 100) * $item->quantity;
        }), 1);

        // Coba Biteship
        $useFallback = true;
        $rates       = collect();

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . config('biteship.api_key'),
            ])->post(config('biteship.base_url') . '/v1/rates/couriers', [
                'origin_area_id'      => config('biteship.origin_area_id'),
                'destination_area_id' => $request->destination_area_id,
                'couriers'            => 'jne,j&t,sicepat,anteraja,pos,tiki',
                'items'               => [[
                    'name'     => 'Paket Kartu Pokemon',
                    'value'    => (int) $cart->items->sum(fn($i) => $i->product->price * $i->quantity),
                    'weight'   => $totalWeight,
                    'quantity' => 1,
                ]],
            ]);

            Log::info('Biteship rates response', [
                'status' => $response->status(),
                'body'   => $response->json(),
                'origin' => config('biteship.origin_area_id'),
                'dest'   => $request->destination_area_id,
                'weight' => $totalWeight,
            ]);

            if ($response->successful() && ($response->json('success') !== false)) {
                $rates = collect($response->json('pricing') ?? [])
                    ->map(fn($rate) => [
                        'courier_code'  => $rate['courier_code'],
                        'courier_name'  => $rate['courier_name'],
                        'service_code'  => $rate['courier_service_code'],
                        'service_name'  => $rate['courier_service_name'],
                        'price'         => $rate['price'],
                        'duration'      => $rate['shipment_duration_range'] ?? '-',
                        'duration_unit' => $rate['shipment_duration_unit'] ?? 'days',
                    ])
                    ->sortBy('price')
                    ->values();

                $useFallback = $rates->isEmpty();
            }
        } catch (\Exception $e) {
            Log::warning('Biteship exception: ' . $e->getMessage());
        }

        // Fallback statis
        if ($useFallback) {
            Log::warning('Biteship fallback aktif');
            $rates = collect([
                ['courier_code' => 'pos',      'courier_name' => 'POS Indonesia', 'service_code' => 'REG',  'service_name' => 'Reguler',  'price' => 10000, 'duration' => '3-5', 'duration_unit' => 'days'],
                ['courier_code' => 'anteraja', 'courier_name' => 'Anteraja',      'service_code' => 'REG',  'service_name' => 'Reguler',  'price' => 12000, 'duration' => '2-4', 'duration_unit' => 'days'],
                ['courier_code' => 'sicepat',  'courier_name' => 'SiCepat',       'service_code' => 'BEST', 'service_name' => 'BEST',     'price' => 13000, 'duration' => '2-3', 'duration_unit' => 'days'],
                ['courier_code' => 'j&t',      'courier_name' => 'J&T Express',   'service_code' => 'EZ',   'service_name' => 'Express',  'price' => 14000, 'duration' => '2-3', 'duration_unit' => 'days'],
                ['courier_code' => 'jne',      'courier_name' => 'JNE',           'service_code' => 'REG',  'service_name' => 'Reguler',  'price' => 15000, 'duration' => '2-3', 'duration_unit' => 'days'],
                ['courier_code' => 'jne',      'courier_name' => 'JNE',           'service_code' => 'YES',  'service_name' => 'YES',      'price' => 25000, 'duration' => '1',   'duration_unit' => 'days'],
            ]);
        }

        return response()->json([
            'rates'        => $rates,
            'total_weight' => $totalWeight,
            'fallback'     => $useFallback,
        ]);
    }
}
