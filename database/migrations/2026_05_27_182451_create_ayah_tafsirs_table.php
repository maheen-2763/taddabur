<?php
// database/migrations/2024_01_01_000009_create_ayah_tafsirs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ayah_tafsirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ayah_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tafsir_id')->constrained()->cascadeOnDelete();
            $table->longText('text');                      // full tafsir explanation
            $table->timestamps();

            $table->unique(['ayah_id', 'tafsir_id']);
            $table->index('tafsir_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ayah_tafsirs');
    }
};
