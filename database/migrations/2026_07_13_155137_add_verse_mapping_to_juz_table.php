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
        Schema::table('juz', function (Blueprint $table) {
            $table->json('verse_mapping')->nullable()->after('number');
        });
    }

    public function down(): void
    {
        Schema::table('juz', function (Blueprint $table) {
            $table->dropColumn('verse_mapping');
        });
    }
};
