<?php
// database/migrations/2024_01_01_000008_create_tafsirs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tafsirs', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Tafsir Ibn Kathir
            $table->string('scholar');                     // Ibn Kathir
            $table->string('language_code');               // en, ar, ur
            $table->string('language_name');               // English
            $table->string('slug')->unique();              // ibn-kathir-en
            $table->string('source')->nullable();          // API identifier
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tafsirs');
    }
};
