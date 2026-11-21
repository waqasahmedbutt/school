<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = ['exam_report_id','student_id','subject_id','section_id','marks_obtained','total_marks'];

    public function exam() {
        return $this->belongsTo(ExamReport::class, 'exam_report_id');
    }

    public function student() {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }
}

