<?php
// database/migrations/2024_01_01_000016_create_reading_progress_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Quran progress
            $table->foreignId('last_ayah_id')->nullable()->constrained('ayahs')->nullOnDelete();
            $table->integer('quran_ayahs_read')->default(0);   // total ayahs read

            // Story progress
            $table->foreignId('story_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('last_chapter_id')->nullable()->constrained('story_chapters')->nullOnDelete();

            // Streaks
            $table->integer('reading_streak_days')->default(0);
            $table->date('last_read_date')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'story_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_progress');
    }
};
