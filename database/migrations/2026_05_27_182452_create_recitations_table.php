<?php
// database/migrations/2024_01_01_000010_create_recitations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recitations', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Mishary Rashid Al-Afasy
            $table->string('slug')->unique();              // mishary-rashid
            $table->string('style')->nullable();           // murattal, mujawwad
            $table->string('audio_url_pattern');           // base URL pattern for audio files
            // e.g. https://everyayah.com/data/Alafasy_128kbps/{surah_padded}{ayah_padded}.mp3
            $table->string('photo')->nullable();
            $table->boolean('is_free')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recitations');
    }
};
