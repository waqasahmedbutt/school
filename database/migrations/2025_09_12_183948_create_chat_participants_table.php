<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatParticipantsTable extends Migration
{
    public function up()
    {
        Schema::create('chat_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // role snapshot (teacher/student/parent) to optimise queries
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['chat_id','user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_participants');
    }
}
