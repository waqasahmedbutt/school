<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = ['section_id', 'start_time', 'end_time', 'room'];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time'   => 'datetime:H:i:s',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function entries()
    {
        return $this->hasMany(TimetableEntry::class);
    }
}
