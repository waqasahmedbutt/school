<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamReport extends Model
{
    use HasFactory;

    protected $fillable = ['classroom_id','title','description','exam_date'];
  protected $casts = [
        'exam_date' => 'datetime',  // ✅ this fixes your issue
    ];
    public function classroom() {
        return $this->belongsTo(Classroom::class);
    }

    public function results() {
        return $this->hasMany(ExamResult::class);
    }
}
