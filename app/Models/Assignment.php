<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'total_marks',
        'created_by',
        'classroom_id',
        'section_id',
        'subject_id',   // ✅ added
        'due_at',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
        'due_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()   // ✅ new relation
    {
        return $this->belongsTo(Subject::class);
    }
}
