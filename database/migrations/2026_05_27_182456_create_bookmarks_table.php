<?php
// database/migrations/2024_01_01_000014_create_bookmarks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('bookmarkable_type');           // App\Models\Ayah or App\Models\StoryChapter
            $table->unsignedBigInteger('bookmarkable_id');
            $table->string('label')->nullable();           // user's custom label
            $table->timestamps();

            // Polymorphic index
            $table->index(['bookmarkable_type', 'bookmarkable_id']);
            // Prevent duplicate bookmarks
            $table->unique(['user_id', 'bookmarkable_type', 'bookmarkable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
