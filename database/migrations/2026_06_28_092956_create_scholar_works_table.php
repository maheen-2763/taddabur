<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scholar_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholar_id')
                ->constrained('scholars')
                ->onDelete('cascade');
            $table->string('title', 200);
            $table->string('arabic_title', 200)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholar_works');
    }
};
