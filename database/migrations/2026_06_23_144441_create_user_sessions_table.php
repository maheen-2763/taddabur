<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Set on login / app open
            $table->timestamp('started_at');

            // Set on logout or after 30min inactivity via scheduler
            $table->timestamp('ended_at')->nullable();

            // Computed: (ended_at - started_at) in minutes
            // Update this when ended_at is set
            $table->unsignedInteger('duration_minutes')->default(0);

            // Optional: which device / platform
            $table->string('platform')->nullable(); // 'web' | 'mobile'

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
