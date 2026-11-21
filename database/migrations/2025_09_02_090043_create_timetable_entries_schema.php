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
  // database/migrations/xxxx_xx_xx_create_timetable_entries_table.php
Schema::create('timetable_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('section_id')->constrained()->onDelete('cascade'); // <-- add this
    $table->foreignId('subject_id')->constrained()->onDelete('cascade');
    $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
    $table->tinyInteger('day_of_week'); // 0=Sun ... 6=Sat
    $table->time('start_time');
    $table->time('end_time');
    $table->string('room')->nullable();
    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
