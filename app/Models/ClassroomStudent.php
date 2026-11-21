<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ClassroomStudent extends Model
{
use HasFactory;


protected $table = 'classroom_student';
protected $fillable = ['classroom_id','section_id','student_id','roll_no'];


public function classroom() { return $this->belongsTo(Classroom::class); }
public function student() { return $this->belongsTo(User::class, 'student_id'); }
public function section() { return $this->belongsTo(Section::class); }
public function examResults() {
    return $this->hasMany(ExamResult::class, 'student_id', 'student_id');
}
}