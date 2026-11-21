<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\Attendance;
use Carbon\Carbon;
use DB;

class AttendanceController extends Controller
{
    /**
     * Index page: show classrooms teacher is assigned to
     */
    public function index()
    {
        $teacherId = auth()->id();

        $assignments = TeacherAssignment::with(['classroom','section','subject'])
            ->where('teacher_id', $teacherId)
            ->get();

        $classrooms = $assignments->pluck('classroom')->unique('id')->values();

        return view('teacher.attendance.index', compact('classrooms', 'assignments'));
    }

    /**
     * Select Section & Subject for a Classroom
     */
    public function selectSection(Classroom $classroom)
    {
        $teacherId = auth()->id();
        $sections = TeacherAssignment::where('teacher_id', $teacherId)
            ->where('classroom_id', $classroom->id)
            ->with('section','subject')
            ->get()
            ->groupBy('section.id');

        return view('teacher.attendance.select_section', compact('classroom','sections'));
    }

    /**
     * Show attendance records and Add New Attendance
     */
    public function records(Request $request, Classroom $classroom, Section $section)
    {
        $teacherId = auth()->id();

        // Teacher subjects in this section
        $subjects = TeacherAssignment::where('teacher_id', $teacherId)
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->with('subject')
            ->get();

        $subjectId = $request->query('subject_id') ?? $subjects->first()->subject_id ?? null;

        // Attendance summary
        $summary = Attendance::select('date')
            ->selectRaw("COUNT(student_id) as total_students")
            ->selectRaw("SUM(status='present') as presents")
            ->selectRaw("SUM(status='absent') as absents")
            ->selectRaw("SUM(status='late') as lates")
            ->selectRaw("SUM(status='excused') as excused")
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->where('subject_id', $subjectId)
            ->groupBy('date')
            ->orderBy('date','desc')
            ->paginate(15);

        return view('teacher.attendance.records', compact('classroom','section','subjects','subjectId','summary'));
    }

    /**
     * Attendance Form: Add/Edit Attendance
     */
    public function attendanceform(Request $request, Classroom $classroom, Section $section)
    {
        $teacherId = auth()->id();

        // Teacher subjects in this section
        $myAssignments = TeacherAssignment::where('teacher_id', $teacherId)
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->with('subject')
            ->get();

        $subjectId = $request->query('subject_id') ?? $myAssignments->first()->subject_id ?? null;
        $date = $request->query('date', now()->toDateString());

        // Students in this section
        $students = $section->students()->orderBy('name')->get();

        // Existing attendance keyed by student+subject
        $existing = Attendance::where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->where('subject_id', $subjectId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy(fn($att) => $att->student_id);

        return view('teacher.attendance.form', compact('classroom','section','students','myAssignments','subjectId','date','existing'));
    }

    /**
     * Store Attendance
     */
    public function storeAttendance(Request $request, Classroom $classroom, Section $section)
    {
        $teacherId = auth()->id();

        $request->validate([
            'date' => 'required|date',
            'subject_id' => 'required|exists:subjects,id',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes' => 'nullable|string',
        ]);

        $date = Carbon::parse($request->date)->toDateString();
        $subjectId = $request->subject_id;

        DB::transaction(function() use ($request, $classroom, $section, $date, $teacherId, $subjectId) {
            foreach ($request->attendance as $studentId => $data) {
                Attendance::updateOrCreate(
                    [
                        'classroom_id' => $classroom->id,
                        'section_id' => $section->id,
                        'student_id' => (int)$studentId,
                        'date' => $date,
                        'subject_id' => $subjectId,
                    ],
                    [
                        'status' => $data['status'],
                        'notes' => $data['notes'] ?? null,
                        'marked_by' => $teacherId,
                        'teacher_id' => $teacherId,
                    ]
                );
            }
        });

        return redirect()->route('teacher.attendance.records', [$classroom->id, $section->id])
            ->with('success', 'Attendance saved.');
    }

    /**
     * Attendance Details (for a specific date)
     */
    public function details(Request $request, Classroom $classroom, Section $section, $date)
    {
        $subjectId = $request->query('subject_id');

        $records = Attendance::where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->where('subject_id', $subjectId)
            ->whereDate('date', $date)
            ->with('student','teacher')
            ->get();

        return view('teacher.attendance.details', compact('classroom','section','records','date','subjectId'));
    }

    /**
     * Update attendance (edit)
     */
    public function updateAttendance(Request $request, Classroom $classroom, Section $section, $date)
    {
        $teacherId = auth()->id();

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes' => 'nullable|string',
        ]);

        $subjectId = $request->subject_id;
        $date = Carbon::parse($date)->toDateString();

        DB::transaction(function() use ($request, $classroom, $section, $date, $teacherId, $subjectId) {
            foreach ($request->attendance as $studentId => $data) {
                Attendance::updateOrCreate(
                    [
                        'classroom_id' => $classroom->id,
                        'section_id' => $section->id,
                        'student_id' => (int)$studentId,
                        'date' => $date,
                        'subject_id' => $subjectId,
                    ],
                    [
                        'status' => $data['status'],
                        'notes' => $data['notes'] ?? null,
                        'marked_by' => $teacherId,
                        'teacher_id' => $teacherId,
                    ]
                );
            }
        });

        return redirect()->route('teacher.attendance.details', [$classroom->id, $section->id, $date])
            ->with('success','Attendance updated.');
    }
public function attendanceDetails(Classroom $classroom, Section $section, $date, Request $request)
{
    $teacherId = auth()->id();

    $subjectId = $request->query('subject_id'); // get from query string

    $records = Attendance::where('classroom_id', $classroom->id)
        ->where('section_id', $section->id)
        ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
        ->whereDate('date', $date)
        ->with(['student','subject','teacher'])
        ->get();

    // Pass $subjectId to view
    return view('teacher.attendance.details', [
        'classroom' => $classroom,
        'section'   => $section,
        'date'      => $date,
        'records'   => $records,
        'subjectId' => $subjectId,
    ]);
}

}
