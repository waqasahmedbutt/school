<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PhysicalExam extends Model
{
use HasFactory;


protected $fillable = ['title','classroom_id','section_id','created_by','exam_date','total_marks','subject_id'];
protected $casts = ['exam_date' => 'date'];


public function classroom() { return $this->belongsTo(Classroom::class); }
public function section() { return $this->belongsTo(Section::class); }
public function marks() { return $this->hasMany(PhysicalExamMark::class, 'physical_exam_id'); }
 public function subject() { return $this->belongsTo(Subject::class, 'subject_id'); }
}

