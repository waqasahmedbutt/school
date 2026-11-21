<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class School extends Model
{
    use HasFactory;

    protected $fillable = ['name','code','address','phone'];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    // Get all sections through classrooms
    public function sections()
    {
        return $this->hasManyThrough(Section::class, Classroom::class);
    }

    // Get all students via classrooms → classroom_student pivot
    public function students()
    {
        return $this->hasManyThrough(ClassroomStudent::class, Classroom::class);
    }
}
