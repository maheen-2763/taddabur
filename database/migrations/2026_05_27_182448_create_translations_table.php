<?php
// database/migrations/2024_01_01_000006_create_translations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Sahih International
            $table->string('author');                      // Muhammad Taqi-ud-Din al-Hilali
            $table->string('language_code');               // en, ur, ar, tr, ms
            $table->string('language_name');               // English, Urdu, Arabic...
            $table->string('slug')->unique();              // sahih-international
            $table->string('source')->nullable();          // API identifier (quran.com ID)
            $table->boolean('is_free')->default(false);    // available on free plan?
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
