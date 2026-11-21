<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('content')->nullable();
            $table->json('meta')->nullable(); // attachments, type, etc.
            $table->boolean('seen')->default(false);
            $table->timestamps();

            $table->index(['chat_id','created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
