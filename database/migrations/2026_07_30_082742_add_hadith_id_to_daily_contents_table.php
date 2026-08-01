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
        Schema::table('daily_contents', function (Blueprint $table) {
            $table->foreignId('hadith_id')->nullable()->after('story_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('daily_contents', function (Blueprint $table) {
            $table->dropForeign(['hadith_id']);
            $table->dropColumn('hadith_id');
        });
    }
};
