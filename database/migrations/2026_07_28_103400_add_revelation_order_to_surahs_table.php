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
            $table->unsignedTinyInteger('revelation_order')->nullable()->after('number');
        });
    }

    public function down(): void
    {
        Schema::table('surahs', function (Blueprint $table) {
            $table->dropColumn('revelation_order');
        });
    }
};
