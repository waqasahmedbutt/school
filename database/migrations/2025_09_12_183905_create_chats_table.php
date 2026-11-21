<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            // optional classroom id - allows teacher to start chats within a specific classroom
            $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('cascade');
            // owner/creator
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['private','classroom'])->default('private');
            $table->string('subject')->nullable(); // optional label
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
