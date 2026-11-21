<?php


namespace App\Models;


use Illuminate\Contract\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable
{
use Notifiable, SoftDeletes, HasFactory;


protected $fillable = [
'name', 'email', 'password', 'role', 'cnic', 'parent_cnic','phone', 'bio', 'avatar', 'is_active', 'email_verified_at'
];


protected $hidden = ['password', 'remember_token'];


protected $casts = [
'email_verified_at' => 'datetime',
'is_active' => 'boolean',
];


// Roles helper
public function isAdmin(): bool { return $this->role === 'admin'; }
public function isTeacher(): bool { return $this->role === 'teacher'; }
public function isStudent(): bool { return $this->role === 'student'; }
public function isParent(): bool { return $this->role === 'parent'; }


// Parent / child relationship via guardians pivot
public function parent()
{
    return $this->belongsTo(User::class, 'parent_id');
}

public function children()
{
    return $this->hasMany(User::class, 'parent_id');
}


// Student's classrooms
public function classrooms(): BelongsToMany
{
return $this->belongsToMany(Classroom::class, 'classroom_student', 'student_id', 'classroom_id')
->withPivot('section_id', 'roll_no')
->withTimestamps();
}


// Teacher assignments
public function teacherAssignments(): HasMany
{
return $this->hasMany(TeacherAssignment::class, 'teacher_id');
}


// Created resources
public function assignmentsCreated(): HasMany
{
return $this->hasMany(Assignment::class, 'created_by');
}


public function quizzesCreated(): HasMany
{
return $this->hasMany(Quiz::class, 'created_by');
}


public function messagesSent(): HasMany
{
return $this->hasMany(Message::class, 'sender_id');
}

public function pivotSection()
{
    return $this->belongsTo(Section::class, 'section_id', 'id');
}


}