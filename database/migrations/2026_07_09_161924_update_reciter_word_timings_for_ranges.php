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
        Schema::table('reciter_word_timings', function (Blueprint $table) {
            $table->dropUnique(['reciter_id', 'surah_number', 'ayah_number', 'word_index']);
        });

        Schema::table('reciter_word_timings', function (Blueprint $table) {
            $table->renameColumn('word_index', 'word_start_index');
            $table->unsignedSmallInteger('word_end_index')->after('word_start_index');
        });

        Schema::table('reciter_word_timings', function (Blueprint $table) {
            $table->unique(
                ['reciter_id', 'surah_number', 'ayah_number', 'word_start_index'],
                'reciter_word_timings_start_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('reciter_word_timings', function (Blueprint $table) {
            $table->dropUnique('reciter_word_timings_start_unique');
            $table->dropColumn('word_end_index');
            $table->renameColumn('word_start_index', 'word_index');
        });

        Schema::table('reciter_word_timings', function (Blueprint $table) {
            $table->unique(['reciter_id', 'surah_number', 'ayah_number', 'word_index']);
        });
    }
};
