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
        Schema::create('prophet_names', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');           // Arabic naam
            $table->string('name_transliteration'); // "Muhammad", "Al-Mahi"
            $table->string('meaning');            // "The Praised One"
            $table->enum('tier', ['name', 'title']); // Tier 1 vs Tier 2
            $table->string('source_type');        // "quran" ya "hadith"
            $table->string('source_reference');   // "Quran 3:144" ya "Sahih Bukhari #3532"
            $table->foreignId('hadith_id')->nullable()->constrained()->nullOnDelete(); // agar hadith se hai, deep-link ke liye
            $table->integer('sort_order');        // display order
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prophet_names');
    }
};
