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
        Schema::table('hadith_collections', function (Blueprint $table) {
            $table->string('scholar_arabic_name')->nullable()->after('scholar');
            $table->string('scholar_years')->nullable()->after('scholar_arabic_name');  // e.g. "194–256 AH"
            $table->text('scholar_bio')->nullable()->after('scholar_years');
        });
    }

    public function down(): void
    {
        Schema::table('hadith_collections', function (Blueprint $table) {
            $table->dropColumn(['scholar_arabic_name', 'scholar_years', 'scholar_bio']);
        });
    }
};
