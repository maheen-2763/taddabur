<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('hadith_chapters', function (Blueprint $table) {
            $table->unsignedInteger('start_number')->nullable()->after('number');
            $table->unsignedInteger('end_number')->nullable()->after('start_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hadith_chapters', function (Blueprint $table) {
            //
        });
    }
};
