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
            $table->json('all_references')->nullable()->after('ayah_id');
        });
    }

    public function down(): void
    {
        Schema::table('prophet_names', function (Blueprint $table) {
            $table->dropColumn('all_references');
        });
    }
};
