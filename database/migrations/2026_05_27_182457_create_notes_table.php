<?php
// database/migrations/2024_01_01_000015_create_notes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ayah_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('story_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('color')->default('#fef3c7'); // highlight color
            $table->boolean('is_private')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'ayah_id', 'story_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
