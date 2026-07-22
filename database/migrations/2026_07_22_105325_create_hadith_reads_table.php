<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('hadith_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hadith_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->useCurrent();
            $table->unique(['user_id', 'hadith_id']); // 👈 duplicate mark rokta hai
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hadith_reads');
    }
};
