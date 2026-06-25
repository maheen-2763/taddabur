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
        Schema::create('hadith_collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // e.g. "Sahih Bukhari"
            $table->string('arabic_name');       // صحيح البخاري
            $table->string('slug')->unique();    // bukhari, muslim, abudawud
            $table->string('scholar');           // Imam Muhammad al-Bukhari
            $table->string('period')->nullable(); // e.g. "9th century CE"
            $table->integer('total_hadith')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hadith_collections');
    }
};
