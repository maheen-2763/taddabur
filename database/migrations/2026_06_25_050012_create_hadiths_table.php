<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hadiths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('hadith_collections')->cascadeOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('hadith_chapters')->nullOnDelete();
            $table->integer('number');           // hadith number within collection
            $table->text('arabic');
            $table->text('english');
            $table->string('narrator_chain')->nullable(); // isnad summary
            $table->string('grade')->nullable();          // sahih, hasan, da'if
            $table->string('grade_source')->nullable();   // who graded it (e.g. "Al-Albani")
            $table->timestamps();

            $table->unique(['collection_id', 'number']); // for updateOrCreate
            $table->index(['collection_id', 'grade']);   // for filtering by authenticity
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hadiths');
    }
};
