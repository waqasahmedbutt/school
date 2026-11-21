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
       Schema::create('exam_reports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
    $table->string('title'); // e.g. "First Term", "Mid Term"
    $table->text('description')->nullable();
    $table->date('exam_date')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_reports');
    }
};
