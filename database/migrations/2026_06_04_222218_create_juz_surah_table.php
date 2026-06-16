<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('juz_surah', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('juz'); // 1–30
            $table->foreignId('surah_id')->constrained()->cascadeOnDelete();

            $table->timestamps();

            $table->index(['juz', 'surah_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('juz_surah');
    }
};
