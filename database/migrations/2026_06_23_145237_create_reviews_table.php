<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // 1–5 star rating
            $table->unsignedTinyInteger('rating');

            $table->text('comment')->nullable();

            // Which area they're reviewing
            $table->enum('category', [
                'general',
                'quran_reader',
                'tafsir',
                'prophet_stories',
                'audio',
            ])->default('general');

            // Soft-delete so you can hide without losing data
            $table->softDeletes();
            $table->timestamps();

            // One review per user (can be changed to allow multiple)
            $table->unique('user_id');

            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
