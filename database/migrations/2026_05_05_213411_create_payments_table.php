<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public $withinTransaction = false;
    /**
     * Run the migrations.
     */
    public function up(): void
{
   
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->cascadeOnDelete();
        $table->string('transaction_id')->nullable(); // dari Midtrans
        $table->string('payment_method')->nullable(); // gopay, bca, dll
        $table->decimal('amount', 15, 2);
        $table->enum('status', ['pending', 'success', 'failed', 'expired'])->default('pending');
        $table->json('payload')->nullable(); // raw response Midtrans
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
