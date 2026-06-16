<?php
// database/migrations/2024_01_01_000017_create_daily_contents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_contents', function (Blueprint $table) {
            $table->id();
            $table->string('type');                        // ayah, story, hadith
            $table->foreignId('ayah_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('story_id')->nullable()->constrained()->nullOnDelete();
            $table->date('scheduled_for')->unique();       // one per day
            $table->text('reflection')->nullable();        // admin-written reflection
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_contents');
    }
};
