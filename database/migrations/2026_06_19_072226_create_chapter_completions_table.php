<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapter_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('story_id')->constrained()->cascadeOnDelete();
            $table->foreignId('story_chapter_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();

            // One completion record per user per chapter — no duplicates
            $table->unique(['user_id', 'story_chapter_id']);

            // Fast lookups: "all completions for this user in this story"
            $table->index(['user_id', 'story_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_completions');
    }
};
