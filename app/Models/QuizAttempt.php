<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class QuizAttempt extends Model
{
use HasFactory;


  protected $casts = [
        'answers'     => 'array',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',  // ✅ this fixes your issue
    ];
protected $fillable = [
    'quiz_id', 'student_id', 'started_at', 'finished_at', 'answers', 'score'
];

public function quiz() { return $this->belongsTo(Quiz::class); }
public function student() { return $this->belongsTo(User::class, 'student_id'); }
}