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
        Schema::table('surahs', function (Blueprint $table) {
            $table->unsignedTinyInteger('juz_start')->default(1);
            $table->unsignedTinyInteger('juz_end')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('surahs', function (Blueprint $table) {
            $table->dropColumn(['juz_start', 'juz_end']);
        });
    }
};
