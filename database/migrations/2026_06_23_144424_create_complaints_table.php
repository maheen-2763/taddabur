<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Short summary shown in the admin table
            $table->string('subject');

            // Full complaint text
            $table->text('message');

            // Category helps the AI chatbot route correctly later
            $table->enum('category', [
                'payment',
                'subscription',
                'content',
                'account',
                'bug',
                'other',
            ])->default('other');

            $table->enum('status', [
                'open',
                'in_progress',
                'resolved',
            ])->default('open');

            // Admin reply stored here
            $table->text('admin_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
