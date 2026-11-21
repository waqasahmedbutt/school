<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;

     protected $fillable = [
        'title',
        'description',
        'created_by',
        'classroom_id',
        'section_id',
        'subject_id',   // ✅ added
        'duration_minutes',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    // ✅ Missing relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
  public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
