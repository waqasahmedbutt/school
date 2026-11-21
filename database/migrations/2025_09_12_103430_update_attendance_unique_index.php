<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // drop the old unique index
            $table->dropUnique('attendance_unique');

            // add new unique index including subject_id
            $table->unique(['classroom_id','section_id','student_id','date','subject_id'], 'attendances_unique');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // drop the new unique index
            $table->dropUnique('attendances_unique');

            // restore old unique index
            $table->unique(['classroom_id','section_id','student_id','date'], 'attendance_unique');
        });
    }
};
