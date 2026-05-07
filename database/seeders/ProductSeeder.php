<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryId = DB::table('categories')->first()?->id;
        if (!$categoryId) return;

        $unitId = DB::table('units')->first()?->id;

        $products = [
            ['Pikachu VMAX', 'Rp 150.000'],
            ['Charizard ex', 'Rp 850.000'],
            ['Mewtwo V', 'Rp 200.000'],
            ['Eevee Heroes Box', 'Rp 450.000'],
            ['Rayquaza VMAX Rainbow', 'Rp 1.200.000'],
            ['Umbreon VMAX Alt Art', 'Rp 2.500.000'],
            ['Booster Pack SV', 'Rp 75.000'],
            ['Elite Trainer Box Paldea', 'Rp 650.000'],
        ];

        foreach ($products as [$name, $priceStr]) {
            $price = (int) str_replace(['Rp ', '.', ','], '', $priceStr);
            DB::table('products')->insertOrIgnore([
                'name'         => $name,
                'slug'         => Str::slug($name),
                'sku'          => strtoupper(Str::random(8)),
                'category_id'  => $categoryId,
                'unit_id'      => $unitId,
                'price'        => $price,
                'cost_price'   => $price * 0.7,
                'stock'        => rand(5, 50),
                'min_stock'    => 3,
                'is_active'    => true,
                'is_published' => true,
                'weight'       => 50,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}