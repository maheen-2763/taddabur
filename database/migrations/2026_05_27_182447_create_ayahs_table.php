<?php
// database/migrations/2024_01_01_000005_create_ayahs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ayahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surah_id')->constrained()->cascadeOnDelete();
            $table->integer('number');                     // verse number in surah
            $table->integer('number_in_quran');            // absolute verse number (1–6236)
            $table->text('text_arabic');                   // Arabic text
            $table->text('text_arabic_simple')->nullable(); // without tashkeel (diacritics)
            $table->integer('page')->nullable();            // Mushaf page number
            $table->integer('juz')->nullable();             // Juz number (1–30)
            $table->integer('hizb')->nullable();            // Hizb number
            $table->integer('ruku')->nullable();            // Ruku number
            $table->boolean('sajda')->default(false);       // prostration ayah
            $table->timestamps();

            $table->unique(['surah_id', 'number']);
            $table->index('number_in_quran');
            $table->index('juz');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ayahs');
    }
};
