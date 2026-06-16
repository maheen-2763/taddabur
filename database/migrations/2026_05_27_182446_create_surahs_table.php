<?php
// database/migrations/2024_01_01_000004_create_surahs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surahs', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->unique();           // 1–114
            $table->string('name_arabic');                 // الفاتحة
            $table->string('name_transliteration');        // Al-Fatihah
            $table->string('name_english');                // The Opening
            $table->string('revelation_type');             // meccan or medinah
            $table->integer('ayah_count');                 // number of verses
            $table->integer('page_number')->nullable();    // Mushaf page
            $table->text('description')->nullable();       // Brief intro to surah
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surahs');
    }
};
