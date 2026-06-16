<?php
// database/migrations/2024_01_01_000002_create_plans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Free, Basic, Premium
            $table->string('slug')->unique();              // free, basic, premium
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 8, 2)->default(0.00);
            $table->decimal('price_yearly', 8, 2)->default(0.00);
            $table->decimal('price_lifetime', 8, 2)->default(0.00);
            $table->string('stripe_monthly_price_id')->nullable();  // Stripe price ID
            $table->string('stripe_yearly_price_id')->nullable();
            $table->json('features');                      // list of feature keys
            $table->integer('story_limit')->default(5);   // -1 = unlimited
            $table->integer('translation_limit')->default(1); // -1 = unlimited
            $table->boolean('has_tafsir')->default(false);
            $table->boolean('has_audio')->default(false);
            $table->boolean('has_notes')->default(false);
            $table->boolean('has_progress')->default(false);
            $table->boolean('has_downloads')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
