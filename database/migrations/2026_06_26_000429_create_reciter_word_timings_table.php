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
        Schema::create('reciter_word_timings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reciter_id')->constrained('recitations')->cascadeOnDelete();
            $table->unsignedSmallInteger('surah_number');
            $table->unsignedSmallInteger('ayah_number');
            $table->unsignedSmallInteger('word_index');
            $table->unsignedInteger('start_ms');
            $table->unsignedInteger('end_ms');
            $table->timestamps();

            $table->index(['reciter_id', 'surah_number', 'ayah_number']);
            $table->unique(['reciter_id', 'surah_number', 'ayah_number', 'word_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reciter_word_timings');
    }
};
