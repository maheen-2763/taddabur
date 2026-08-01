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
            $table->dropUnique(['scheduled_for']); // purana single-column unique hatao
            $table->unique(['scheduled_for', 'type']); // naya composite unique
        });
    }

    public function down(): void
    {
        Schema::table('daily_contents', function (Blueprint $table) {
            $table->dropUnique(['scheduled_for', 'type']);
            $table->unique('scheduled_for');
        });
    }
};
