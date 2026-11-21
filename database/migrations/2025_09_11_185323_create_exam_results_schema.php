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
       Schema::create('exam_results', function (Blueprint $table) {
    $table->id();
    $table->foreignId('exam_report_id')->constrained()->onDelete('cascade');
    $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('subject_id')->constrained()->onDelete('cascade');
    $table->integer('marks_obtained')->nullable();
    $table->integer('total_marks')->default(100);
    $table->timestamps();

    $table->unique(['exam_report_id','student_id','subject_id']); // prevent duplicate
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
