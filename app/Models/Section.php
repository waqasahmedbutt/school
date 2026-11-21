<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Section extends Model
{
use HasFactory;


protected $fillable = ['classroom_id','name','code','capacity'];


public function classroom() { return $this->belongsTo(Classroom::class); }
public function students()
{
    // Pull only students assigned to this section via classroom_student pivot
    return $this->belongsToMany(User::class, 'classroom_student', 'section_id', 'student_id')
                ->withPivot('classroom_id','roll_no')
                ->withTimestamps();
}
public function teacherAssignments()
{
    return $this->hasMany(TeacherAssignment::class);
}
public function timetableEntries() { return $this->hasMany(TimetableEntry::class); }

}