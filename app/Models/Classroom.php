<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Classroom extends Model
{
use HasFactory;


protected $fillable = ['school_id','name','slug','description','capacity','created_by'];


public function school() { return $this->belongsTo(School::class); }
public function sections() { return $this->hasMany(Section::class); }
public function students()
{
    return $this->belongsToMany(User::class, 'classroom_student', 'classroom_id', 'student_id')
        ->withPivot(['section_id', 'roll_no'])
        ->withTimestamps();
}

public function teacherAssignments() { return $this->hasMany(TeacherAssignment::class); }

}