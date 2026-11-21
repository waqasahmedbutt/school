<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'classroom_id',
        'section_id',
        'subject_id',   // new
        'teacher_id',   // new
        'student_id',
        'date',
        'status',
        'marked_by',
        'notes',        // keep 'notes' (plural)
    ];

    protected $casts = [
        'date' => 'date',
        'marked_by' => 'integer',
        'subject_id' => 'integer',
        'teacher_id' => 'integer',
    ];

    // Common statuses
    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT  = 'absent';
    public const STATUS_LATE    = 'late';
    public const STATUS_EXCUSED = 'excused';

    public static function statuses(): array
    {
        return [
            self::STATUS_PRESENT,
            self::STATUS_ABSENT,
            self::STATUS_LATE,
            self::STATUS_EXCUSED,
        ];
    }

    /* ----------------- Relationships ----------------- */

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * The teacher who marked/owns this attendance row.
     * (Could be the same as marked_by, but storing teacher_id allows teacher-specific attendance records.)
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /* ----------------- Scopes / Helpers (optional niceties) ----------------- */

    public function scopeForClassSection($q, $classroomId, $sectionId)
    {
        return $q->where('classroom_id', $classroomId)->where('section_id', $sectionId);
    }

    public function scopeForDate($q, $date)
    {
        return $q->whereDate('date', $date);
    }
}
