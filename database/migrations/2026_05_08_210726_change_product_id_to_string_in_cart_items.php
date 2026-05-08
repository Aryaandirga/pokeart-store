<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus data lama dulu (product_id lama tidak valid)
        DB::table('cart_items')->truncate();

        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('product_id')->change();
        });
    }

    public function down(): void
    {
        DB::table('cart_items')->truncate();
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->change();
        });
    }
};
