<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scholars', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('arabic_name', 100);
            $table->smallInteger('birth_ah');
            $table->smallInteger('death_ah');
            $table->enum('madhab', ['hanafi', 'maliki', 'shafi_i', 'hanbali']);
            $table->longText('biography');
            $table->longText('early_life');
            $table->longText('trials')->nullable();
            $table->string('slug', 120)->unique();
            $table->timestamps();

            // Indexes for fast queries
            $table->index('madhab');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholars');
    }
};
