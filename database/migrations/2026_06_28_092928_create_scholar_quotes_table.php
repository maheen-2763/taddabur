<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scholar_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholar_id')
                ->constrained('scholars')
                ->onDelete('cascade');
            $table->text('quote_arabic');
            $table->text('quote_english');
            $table->string('source', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholar_quotes');
    }
};
