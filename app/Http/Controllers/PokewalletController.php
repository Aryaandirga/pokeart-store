<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PokewalletController extends Controller
{
    private string $baseUrl = 'https://api.pokewallet.io';

    private function apiKey(): string
    {
        return config('services.pokewallet.key', '');
    }

    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:1|max:100']);

        $response = Http::withHeaders(['X-API-Key' => $this->apiKey()])
            ->get("{$this->baseUrl}/search", [
                'q'     => $request->q,
                'page'  => $request->get('page', 1),
                'limit' => $request->get('limit', 20),
            ]);

        if ($response->failed()) {
            return response()->json([
                'error'  => 'API request failed',
                'status' => $response->status(),
            ], $response->status());
        }

        return response()->json($response->json());
    }

    public function image(Request $request, string $id)
    {
        $size = in_array($request->get('size'), ['low', 'high']) ? $request->get('size') : 'low';

        $response = Http::withHeaders(['X-API-Key' => $this->apiKey()])
            ->get("{$this->baseUrl}/images/{$id}", ['size' => $size]);

        if ($response->failed()) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        return response($response->body(), 200)
            ->header('Content-Type', $response->header('Content-Type') ?? 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
