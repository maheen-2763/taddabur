<?php
// database/migrations/2024_01_01_000013_create_story_chapters_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('story_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('content');                   // rich HTML content
            $table->integer('order');                      // chapter 1, 2, 3...
            $table->json('quran_references')->nullable();  // ayahs mentioned in this chapter
            $table->json('hadith_references')->nullable(); // hadith cited
            $table->string('image')->nullable();
            $table->timestamps();

            $table->unique(['story_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_chapters');
    }
};
