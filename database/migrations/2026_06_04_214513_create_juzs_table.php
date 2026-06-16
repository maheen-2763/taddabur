<?php

// database/migrations/xxxx_xx_xx_create_juzs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('juzs', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('number')->unique(); // 1–30

            $table->string('name_arabic');
            $table->string('name_english')->nullable();

            // Start boundary
            $table->unsignedSmallInteger('start_surah_id');
            $table->unsignedSmallInteger('start_ayah')->nullable();

            // End boundary
            $table->unsignedSmallInteger('end_surah_id');
            $table->unsignedSmallInteger('end_ayah')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('juzs');
    }
};
