<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->string('reliability')->nullable()->after('grade');
            $table->string('attribution_type')->nullable()->after('reliability');
        });
    }

    public function down(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->dropColumn(['reliability', 'attribution_type']);
        });
    }
};
