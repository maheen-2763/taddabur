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
        Schema::table('juzs', function (Blueprint $table) {
            $table->json('verse_mapping')->nullable()->after('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('juzs', function (Blueprint $table) {
            $table->dropColumn('verse_mapping');
        });
    }
};
