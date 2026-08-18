<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->string('razorpay_order_id')->nullable()->unique()->after('message');
            $table->string('razorpay_payment_id')->nullable()->unique()->after('razorpay_order_id');
            $table->string('payment_method')->default('upi')->after('razorpay_payment_id');
            $table->string('status')->default('success')->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->dropColumn(['razorpay_order_id', 'razorpay_payment_id', 'payment_method', 'status']);
        });
    }
};
