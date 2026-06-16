<?php
// database/migrations/2024_01_01_000011_create_prophets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prophets', function (Blueprint $table) {
            $table->id();
            $table->string('name_arabic');                 // آدم
            $table->string('name_english');                // Adam
            $table->string('name_transliteration');        // Aadam
            $table->string('slug')->unique();              // adam
            $table->string('title')->nullable();           // Father of Humanity
            $table->integer('order')->unique();            // chronological order 1–25
            $table->text('summary')->nullable();           // short intro paragraph
            $table->string('period')->nullable();          // e.g. "circa 4000 BCE"
            $table->string('mentioned_in_quran')->nullable(); // surah references
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prophets');
    }
};
