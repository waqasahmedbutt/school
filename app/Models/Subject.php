<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Subject extends Model
{
use HasFactory;


protected $fillable = ['name','code','description'];

  public function exams()
    {
        return $this->hasMany(PhysicalExam::class, 'subject_id');
    }
public function teacherAssignments() { return $this->hasMany(TeacherAssignment::class); }
}