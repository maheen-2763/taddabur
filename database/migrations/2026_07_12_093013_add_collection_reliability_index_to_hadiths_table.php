<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->index(['collection_id', 'reliability'], 'hadiths_collection_reliability_idx');
            $table->index('reliability', 'hadiths_reliability_idx');
        });
    }

    public function down(): void
    {
        Schema::table('hadiths', function (Blueprint $table) {
            $table->dropIndex('hadiths_collection_reliability_idx');
            $table->dropIndex('hadiths_reliability_idx');
        });
    }
};
