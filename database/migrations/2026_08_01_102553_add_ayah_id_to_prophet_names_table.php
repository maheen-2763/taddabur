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
        Schema::table('prophet_names', function (Blueprint $table) {
            $table->foreignId('ayah_id')->nullable()->after('hadith_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('prophet_names', function (Blueprint $table) {
            $table->dropForeign(['ayah_id']);
            $table->dropColumn('ayah_id');
        });
    }
};
