<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained();
            $table->string('billing');                    // monthly, yearly, lifetime
            $table->string('razorpay_order_id')->unique();
            $table->integer('amount');                     // always in paise, always an integer
            $table->string('currency', 3)->default('INR');
            $table->decimal('exchange_rate_used', 10, 4)->nullable();
            $table->string('status')->default('created');  // created, paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_orders');
    }
};
