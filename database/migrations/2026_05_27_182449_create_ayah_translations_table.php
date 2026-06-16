<?php
// database/migrations/2024_01_01_000007_create_ayah_translations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ayah_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ayah_id')->constrained()->cascadeOnDelete();
            $table->foreignId('translation_id')->constrained()->cascadeOnDelete();
            $table->text('text');                          // translated verse text
            $table->timestamps();

            $table->unique(['ayah_id', 'translation_id']);
            $table->index('translation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ayah_translations');
    }
};
