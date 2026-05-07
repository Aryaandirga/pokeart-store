<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductObserver
{
    /**
     * Saat produk baru dibuat di IMS, otomatis publish ke website
     * jika produk aktif dan punya harga.
     */
    public function created(Product $product): void
    {
        $this->autoPublish($product);
    }

    /**
     * Saat produk diupdate di IMS (misal stok/harga berubah),
     * publish otomatis jika memenuhi syarat.
     */
    public function updated(Product $product): void
    {
        $this->autoPublish($product);
    }

    /**
     * Logic auto-publish:
     * - Produk aktif (is_active = true)
     * - Punya harga > 0
     * - Punya nama
     * Maka is_published = true otomatis.
     *
     * Kalau is_active = false, unpublish dari website juga.
     */
    private function autoPublish(Product $product): void
    {
        $shouldPublish = $product->is_active
            && $product->price > 0
            && !empty($product->name);

        // Hindari infinite loop dengan cek apakah nilai sudah sama
        if ($product->is_published === $shouldPublish) {
            return;
        }

        // Pastikan slug ada
        if (empty($product->slug)) {
            Product::withoutEvents(function () use ($product) {
                $product->update([
                    'is_published' => $shouldPublish,
                    'slug'         => \Illuminate\Support\Str::slug($product->name),
                ]);
            });
        } else {
            Product::withoutEvents(function () use ($product) {
                $product->update(['is_published' => $shouldPublish]);
            });
        }
    }
}
