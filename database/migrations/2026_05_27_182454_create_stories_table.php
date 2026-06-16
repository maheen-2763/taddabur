<?php
// database/migrations/2024_01_01_000012_create_stories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prophet_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');                       // Story of Adam (AS)
            $table->string('slug')->unique();
            $table->string('category');                    // prophet, companion, sahaba, general
            $table->string('subject')->nullable();         // name of companion if category=companion
            $table->text('summary');                       // short excerpt shown in listing
            $table->string('cover_image')->nullable();
            $table->string('difficulty')->default('beginner'); // beginner, intermediate, advanced
            $table->boolean('is_free')->default(false);    // available on free plan?
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->integer('read_time_minutes')->nullable(); // estimated reading time
            $table->json('quran_references')->nullable();  // related ayahs
            $table->json('tags')->nullable();              // searchable tags
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
