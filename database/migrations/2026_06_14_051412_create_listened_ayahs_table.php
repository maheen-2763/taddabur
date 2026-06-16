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
        Schema::create('listened_ayahs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id');
            $table->foreignId('surah_id');
            $table->foreignId('ayah_id');

            $table->timestamps();

            $table->unique([
                'user_id',
                'ayah_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listened_ayahs');
    }
};
