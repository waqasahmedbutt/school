<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classroom_id');
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('student_id');
            $table->date('date');
            $table->string('status', 16)->default('absent'); // present / absent / late
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('marked_by')->nullable(); // teacher user id
            $table->timestamps();

            $table->unique(['classroom_id','section_id','student_id','date'], 'attendance_unique');
            $table->index(['classroom_id','section_id','date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
