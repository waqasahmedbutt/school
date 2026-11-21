<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class QuizQuestion extends Model
{
use HasFactory;


protected $fillable = ['quiz_id','question','type','marks','meta'];
protected $casts = ['meta' => 'array'];


public function quiz() { return $this->belongsTo(Quiz::class); }
public function options() { return $this->hasMany(QuizOption::class, 'question_id'); }
}