<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PhysicalExamMark extends Model
{
use HasFactory;


protected $table = 'physical_exam_marks';
protected $fillable = ['physical_exam_id','student_id','marks_obtained','remarks'];


public function exam() { return $this->belongsTo(PhysicalExam::class, 'physical_exam_id'); }
public function student() { return $this->belongsTo(User::class, 'student_id'); }
}