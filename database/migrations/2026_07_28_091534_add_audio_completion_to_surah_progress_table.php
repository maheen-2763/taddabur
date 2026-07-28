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
        Schema::table('surah_progress', function (Blueprint $table) {
            $table->boolean('is_audio_completed')->default(false)->after('is_completed');
            $table->timestamp('audio_completed_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surah_progresses', function (Blueprint $table) {
            $table->dropColumn(['is_audio_completed', 'audio_completed_at']);
        });
    }
};
