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
        Schema::table('recitations', function (Blueprint $table) {
            Schema::table('recitations', function (Blueprint $table) {
                $table->dropColumn('timing_accuracy');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recitations', function (Blueprint $table) {
            //
        });
    }
};
