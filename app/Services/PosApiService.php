<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PosApiService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.pos_api.url');
        $this->token   = config('services.pos_api.token');
    }

    private function http()
    {
        return Http::withHeaders([
            'X-API-Token' => $this->token,
            'Accept'      => 'application/json',
        ]);
    }

    public function getProducts(array $params = [])
    {
        $response = $this->http()
            ->get("{$this->baseUrl}/api/products", $params);
        if ($response->failed()) return null;
        return $response->json();
    }

    public function getProduct(string $slug)
    {
        $response = $this->http()
            ->get("{$this->baseUrl}/api/products/{$slug}");
        if ($response->failed()) return null;
        return $response->json();
    }

    public function getProductById($id)
    {
        $response = $this->http()
            ->get("{$this->baseUrl}/api/products/{$id}");
        if ($response->failed()) return null;
        return $response->json();
    }

    public function getCategories()
    {
        $response = $this->http()
            ->get("{$this->baseUrl}/api/categories");
        if ($response->failed()) return [];
        return $response->json();
    }

    public function createOrder(array $data)
    {
        $response = $this->http()
            ->post("{$this->baseUrl}/api/orders", $data);
        if ($response->failed()) return null;
        return $response->json();
    }
}