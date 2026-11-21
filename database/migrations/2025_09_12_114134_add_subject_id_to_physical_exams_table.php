<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubjectIdToPhysicalExamsTable extends Migration
{
    public function up()
    {
        Schema::table('physical_exams', function (Blueprint $table) {
            // only add if it doesn't already exist
            if (! Schema::hasColumn('physical_exams', 'subject_id')) {
                // add nullable FK to subjects; null on delete
                $table->foreignId('subject_id')
                      ->nullable()
                      ->after('created_by')
                      ->constrained('subjects')
                      ->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('physical_exams', function (Blueprint $table) {
            if (Schema::hasColumn('physical_exams', 'subject_id')) {
                // drop FK & column (Laravel helper will remove constraint then column)
                $table->dropConstrainedForeignId('subject_id');
            }
        });
    }
}
