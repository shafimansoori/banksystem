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
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Anonymous chats allowed
            $table->string('session_id', 64); // For tracking anonymous sessions
            $table->text('user_message');
            $table->text('bot_response');
            $table->string('intent')->nullable();
            $table->string('sentiment', 20)->default('neutral');
            $table->float('confidence', 3, 3)->default(0.000);
            $table->json('entities')->nullable(); // Store extracted entities
            $table->json('context')->nullable(); // Store conversation context
            $table->string('language', 5)->default('tr'); // Language detection
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'session_id']);
            $table->index(['created_at', 'intent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
