<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prophets', function (Blueprint $table) {
            $table->string('title_arabic')->nullable();
            $table->string('title_transliteration')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('prophets', function (Blueprint $table) {
            $table->dropColumn(['title_arabic', 'title_transliteration']);
        });
    }
};
