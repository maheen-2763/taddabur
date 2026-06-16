<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allah_names', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('position')->unique();

            $table->string('name_ar');
            $table->string('transliteration');

            $table->string('english_name')->nullable();

            $table->text('meaning')->nullable();

            $table->string('reference')->nullable();

            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allah_names');
    }
};
