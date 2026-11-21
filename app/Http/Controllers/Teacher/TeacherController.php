<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherAssignment;
use App\Models\Classroom;
use App\Models\Section;
use App\Models\Assignment;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\Chat;
use App\Models\ClassroomStudent;
use App\Models\Attendance;
use App\Models\PhysicalExam;
use App\Models\ExamReport;
use App\Models\User;
use App\Models\PhysicalExamMark;
use App\Models\TimetableEntry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf; 
use Carbon\Carbon;

class TeacherController extends Controller
{
    // dashboard quick view (notifications + summary)
    public function dashboard()
    {
        $teacherId = auth()->id();

        // classroom list from teacher assignments (group by classroom)
        $assignments = TeacherAssignment::with('classroom','section','subject')
            ->where('teacher_id', $teacherId)
            ->get()
            ->groupBy('classroom_id');

        // unread notifications (database)
        $notifications = auth()->user()->unreadNotifications()->take(10)->get();

        return view('teacher.dashboard', compact('assignments','notifications'));
    }

    // list all classrooms assigned
 public function classrooms()
{
    $teacherId = auth()->id();

    // load teacher assignments (classroom + section + subject)
    $assignments = TeacherAssignment::with('classroom','section','subject')
        ->where('teacher_id', $teacherId)
        ->get();

    // group by composite key classroomId-sectionId
    $groups = $assignments->groupBy(function($item){
        return $item->classroom_id.'-'.$item->section_id;
    });

    // build a list where each item represents one classroom+section
    $classroomSections = $groups->map(function($group) {
        $first = $group->first();
        return (object)[
            'classroom'     => $first->classroom,
            'section'       => $first->section,
            'assignments'   => $group->values(), // teacherAssignments for this pair
            'subjects'      => $group->pluck('subject')->unique('id')->values(),
            'students_count'=> ClassroomStudent::where('classroom_id', $first->classroom_id)
                                ->where('section_id', $first->section_id)
                                ->count(),
        ];
    })->values();

    return view('teacher.classrooms.index', compact('classroomSections'));
}
public function show(User $student)
    {
        $teacherId = auth()->id();

        // Optional: ensure the teacher has at least one assignment in a classroom that contains this student
        $isAssigned = TeacherAssignment::where('teacher_id', $teacherId)
            ->whereIn('classroom_id', function($q) use ($student) {
                $q->select('classroom_id')
                  ->from('classroom_student')
                  ->where('student_id', $student->id);
            })->exists();

        abort_unless($isAssigned, 403, 'You are not allowed to view this student.');

        // load classrooms/section pivot data for this student
        $classrooms = ClassroomStudent::with('classroom','section')
            ->where('student_id', $student->id)
            ->get();

        return view('teacher.students.show', compact('student','classrooms'));
    }




// teacher classroom show (section-aware) — robust version
public function classroomShow(Request $request, Classroom $classroom)
{
    $teacherId = auth()->id();

    $allAssignments = TeacherAssignment::with(['subject','section'])
        ->where('teacher_id', $teacherId)
        ->where('classroom_id', $classroom->id)
        ->get();

    $allAssignments = $allAssignments->filter(fn($ta) => $ta && $ta->subject && $ta->section)->values();

    if ($allAssignments->isEmpty()) {
        abort(403, 'Not assigned to this classroom or no valid assignments.');
    }

    // SECTION
    $section = $request->filled('section_id') ? Section::findOrFail($request->section_id) : $allAssignments->first()->section;

    $myAssignments = $allAssignments->filter(fn($ta) => $ta->section_id == $section->id)->values();

    // SUBJECT filter
    $subjectId = $request->filled('subject_id') ? $request->subject_id : null;

    // Teacher created content filtered by section + optional subject
    $assignmentsQuery = Assignment::where('created_by',$teacherId)
        ->where('classroom_id',$classroom->id)
        ->where('section_id',$section->id)
        ->orderByDesc('created_at');

    $quizzesQuery = Quiz::where('created_by',$teacherId)
        ->where('classroom_id',$classroom->id)
        ->where('section_id',$section->id)
        ->orderByDesc('created_at');

    if ($subjectId) {
        $assignmentsQuery->where('subject_id', $subjectId);
        $quizzesQuery->where('subject_id', $subjectId);
    }

    $assignments = $assignmentsQuery->get();
    $quizzes = $quizzesQuery->get();

    $physicalExams = PhysicalExam::where('created_by',$teacherId)
        ->where('classroom_id',$classroom->id)
        ->where('section_id',$section->id)
        ->orderByDesc('exam_date')
        ->get();

    $students = ClassroomStudent::with('student')
        ->where('classroom_id',$classroom->id)
        ->where('section_id',$section->id)
        ->get();

    $exams = ExamReport::where('classroom_id', $classroom->id)
        ->orderByDesc('exam_date')
        ->get();

    // Recent chats for this teacher within the classroom
    $myChats = Chat::where('classroom_id', $classroom->id)
        ->whereHas('participants', function($q) use ($teacherId) {
            $q->where('user_id', $teacherId);
        })
        ->with([
            'users:id,name,role',
            'messages' => function($q) { $q->latest()->limit(1); }
        ])
        ->orderByDesc('updated_at')
        ->get();

    // Map student_id => parent User (or null)
    $studentParents = [];
    foreach ($students as $s) {
        $stu = $s->student ?? null;
        if ($stu) {
            // if parent relation exists, use it; otherwise null
            $studentParents[$stu->id] = method_exists($stu, 'parent') ? $stu->parent : null;
        }
    }

    return view('teacher.classrooms.show', compact(
        'classroom','section','myAssignments','allAssignments',
        'assignments','quizzes','physicalExams','students','exams','subjectId',
        'myChats','studentParents'
    ));
}


    /* ---------------- ASSIGNMENTS ---------------- */
    public function createAssignment(Classroom $classroom)
    {
        // ensure teacher belongs to this classroom
        $this->authorizeAssignmentAccess($classroom);

        $sections = TeacherAssignment::where('teacher_id', auth()->id())
            ->where('classroom_id', $classroom->id)
            ->with('section','subject')
            ->get();

        return view('teacher.assignments.create', compact('classroom','sections'));
    }

  public function storeAssignment(Request $request, Classroom $classroom)
{
    $this->authorizeAssignmentAccess($classroom);

    $data = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'total_marks' => 'nullable|integer|min:0',
        'due_at' => 'nullable|date',
        'section_id' => 'required|exists:sections,id',
        'subject_id' => 'required|exists:subjects,id', // ✅ new
    ]);

    Assignment::create([
        'title' => $data['title'],
        'description' => $data['description'] ?? null,
        'total_marks' => $data['total_marks'] ?? null,
        'due_at' => $data['due_at'] ?? null,
        'created_by' => auth()->id(),
        'classroom_id' => $classroom->id,
        'section_id' => $data['section_id'],
        'subject_id' => $data['subject_id'],  // ✅ save subject
    ]);

    return redirect()
        ->route('teacher.classrooms.show', $classroom)
        ->with('success','Assignment created.');
}

public function updateAssignment(Request $request, Assignment $assignment)
{
    abort_unless($assignment->created_by == auth()->id(), 403);

    $data = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'total_marks' => 'nullable|integer|min:0',
        'due_at' => 'nullable|date',
        'section_id' => 'required|exists:sections,id',
        'subject_id' => 'required|exists:subjects,id', // ✅ new
    ]);

    $assignment->update($data);

    return redirect()
        ->route('teacher.classrooms.show', $assignment->classroom)
        ->with('success','Assignment updated.');
}


    public function editAssignment(Assignment $assignment)
    {
        abort_unless($assignment->created_by == auth()->id(), 403);
        $sections = TeacherAssignment::where('teacher_id', auth()->id())
            ->where('classroom_id', $assignment->classroom_id)
            ->with('section','subject')
            ->get();

        return view('teacher.assignments.edit', compact('assignment','sections'));
    }

  

    public function destroyAssignment(Assignment $assignment)
    {
        abort_unless($assignment->created_by == auth()->id(), 403);
        $assignment->delete();

        return back()->with('success','Assignment deleted.');
    }

    /* ---------------- QUIZZES (MCQ) ---------------- */
    public function createQuiz(Classroom $classroom)
    {
        $this->authorizeAssignmentAccess($classroom);
        $sections = TeacherAssignment::where('teacher_id', auth()->id())
            ->where('classroom_id', $classroom->id)
            ->with('section','subject')
            ->get();

        return view('teacher.quizzes.create', compact('classroom','sections'));
    }


public function storeQuiz(Request $request, Classroom $classroom)
{
    $this->authorizeAssignmentAccess($classroom);

    $v = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'section_id' => 'required|exists:sections,id',
        'subject_id' => 'required|exists:subjects,id',
        'duration_minutes' => 'nullable|integer|min:1',
        'questions' => 'required|array|min:1',
        'questions.*.question' => 'required|string',
        'questions.*.marks' => 'nullable|numeric',
        'questions.*.options' => 'required|array|min:2',
        'questions.*.options.*.text' => 'required|string',
        'questions.*.options.*.is_correct' => 'nullable|in:0,1',
    ]);

    if ($v->fails()) {
        return back()->withErrors($v)->withInput();
    }

    DB::transaction(function() use ($request, $classroom) {
        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description ?? null,
            'created_by' => auth()->id(),
            'classroom_id' => $classroom->id,
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'duration_minutes' => $request->duration_minutes ?? null,
            'is_published' => false,
        ]);

        foreach ($request->questions as $q) {
            $qq = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $q['question'],
                'marks' => $q['marks'] ?? 0,
                'type' => 'mcq',
                'meta' => null,
            ]);

            foreach ($q['options'] as $opt) {
                QuizOption::create([
                    'question_id' => $qq->id,
                    'option_text' => $opt['text'],
                    'is_correct' => (isset($opt['is_correct']) && $opt['is_correct'] === '1'),
                ]);
            }
        }
    });

    return redirect()->route('teacher.classrooms.show', $classroom)
        ->with('success','Quiz saved. You can publish it when ready.');
}


public function editQuiz(Quiz $quiz)
{
    abort_unless($quiz->created_by == auth()->id(), 403);
    $quiz->load('questions.options');

    $sections = TeacherAssignment::where('teacher_id', auth()->id())
        ->where('classroom_id', $quiz->classroom_id)
        ->with('section','subject')
        ->get();

    return view('teacher.quizzes.edit', compact('quiz','sections'));
}


public function updateQuiz(Request $request, Quiz $quiz)
{
    abort_unless($quiz->created_by == auth()->id(), 403);

    $data = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'duration_minutes' => 'nullable|integer|min:1',
        'section_id' => 'required|exists:sections,id',
        'subject_id' => 'required|exists:subjects,id',
        'questions' => 'array',
        'questions.*.text' => 'required|string',
        'questions.*.options' => 'array',
        'questions.*.options.*.text' => 'required|string',
        'questions.*.correct_option_id' => 'nullable|integer',
    ]);

    DB::transaction(function () use ($quiz, $data) {
        $quiz->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
        ]);

        if (!empty($data['questions'])) {
            foreach ($data['questions'] as $qId => $qData) {
                $question = $quiz->questions()->find($qId);
                if (!$question) continue;

                $question->update(['question' => $qData['text']]);

                if (!empty($qData['options'])) {
                    foreach ($qData['options'] as $optId => $optData) {
                        $option = $question->options()->find($optId);
                        if ($option) {
                            $option->update(['option_text' => $optData['text']]);
                        }
                    }
                }

                if (!empty($qData['correct_option_id'])) {
                    $question->options()->update(['is_correct' => false]);
                    $correctOption = $question->options()->find($qData['correct_option_id']);
                    if ($correctOption) {
                        $correctOption->update(['is_correct' => true]);
                    }
                }
            }
        }
    });

    return redirect()
        ->route('teacher.classrooms.show', $quiz->classroom)
        ->with('success', 'Quiz updated with questions.');
}



    public function destroyQuiz(Quiz $quiz)
    {
        abort_unless($quiz->created_by == auth()->id(), 403);
        $quiz->delete();
        return back()->with('success','Quiz deleted.');
    }

    public function publishQuiz(Quiz $quiz)
    {
        abort_unless($quiz->created_by == auth()->id(), 403);
        $quiz->update(['is_published' => true]);
        return back()->with('success','Quiz published.');
    }

    /* ---------------- ATTENDANCE ---------------- */

public function attendanceForm(Request $request, Classroom $classroom, Section $section)
    {
        $teacherId = auth()->id();

        // authorize teacher for this classroom/section
        $has = TeacherAssignment::where('teacher_id', $teacherId)
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->exists();
        abort_unless($has, 403, 'Not allowed');

        // teacher's assignments (subjects)
        $myAssignments = TeacherAssignment::where('teacher_id', $teacherId)
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->with('subject')
            ->get();

        // load students **only for this section**
        $students = $section->students()->orderBy('name')->get();

        $date = $request->query('date', now()->toDateString());

        // existing attendance keyed by student_id AND subject_id
       // Existing attendance keyed by student + subject
$existing = Attendance::where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->whereDate('date', $date)
            ->when($request->subject_id, function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            })
            ->get()
            ->keyBy(function($item) {
                return $item->student_id . '_' . $item->subject_id;
            });


        return view('teacher.attendance.form', compact(
            'classroom', 'section', 'students', 'date', 'existing', 'myAssignments'
        ));
    }

    /**
     * Store attendance (create/update rows)
     */
    public function storeAttendance(Request $request, Classroom $classroom, Section $section)
    {
        $teacherId = auth()->id();

        // authorize
        $has = TeacherAssignment::where('teacher_id', $teacherId)
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->exists();
        abort_unless($has, 403, 'Not allowed');

        $v = $request->validate([
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
                $status = in_array($data['status'] ?? '', Attendance::statuses())
                    ? $data['status']
                    : Attendance::STATUS_ABSENT;

                Attendance::updateOrCreate(
                    [
                        'classroom_id' => $classroom->id,
                        'section_id'   => $section->id,
                        'student_id'   => (int)$studentId,
                        'date'         => $date,
                        'subject_id'   => $subjectId, // key per subject
                    ],
                    [
                        'status'     => $status,
                        'notes'      => $data['notes'] ?? null,
                        'marked_by'  => $teacherId,
                        'teacher_id' => $teacherId,
                    ]
                );
            }
        });

        return redirect()
            ->route('teacher.attendance.form', [$classroom->id, $section->id, 'date' => $date])
            ->with('success', 'Attendance saved.');
    }

    /**
     * Show attendance records summary (per subject)
     */
    public function attendanceRecords(Request $request, Classroom $classroom, Section $section)
    {
        $teacherId = auth()->id();

        $has = TeacherAssignment::where('teacher_id', $teacherId)
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->exists();
        abort_unless($has, 403, 'Not allowed');

        $summary = Attendance::select('date','subject_id')
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as presents")
            ->selectRaw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absents")
            ->selectRaw("SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as lates")
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->groupBy('date','subject_id')
            ->orderBy('date','desc')
            ->paginate(15);

        return view('teacher.attendance.records', compact('classroom','section','summary'));
    }

    /**
     * Attendance details for a given date & subject
     */
    public function attendanceDetails(Classroom $classroom, Section $section, $date, $subjectId)
    {
        $records = Attendance::where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->whereDate('date', $date)
            ->where('subject_id', $subjectId)
            ->with(['student','subject','teacher'])
            ->get();

        return view('teacher.attendance.details', [
            'classroom' => $classroom,
            'section'   => $section,
            'date'      => $date,
            'records'   => $records,
        ]);
    }

    /**
     * Update attendance for an existing date & subject
     */
    public function updateAttendance(Request $request, Classroom $classroom, Section $section, $date, $subjectId)
    {
        $teacherId = auth()->id();

        $has = TeacherAssignment::where('teacher_id', $teacherId)
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->exists();
        abort_unless($has, 403, 'Not allowed');

        $v = $request->validate([
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes' => 'nullable|string',
        ]);

        $date = Carbon::parse($date)->toDateString();

        DB::transaction(function() use ($request, $classroom, $section, $date, $teacherId, $subjectId) {
            foreach ($request->attendance as $studentId => $data) {
                $status = in_array($data['status'] ?? '', Attendance::statuses())
                    ? $data['status']
                    : Attendance::STATUS_ABSENT;

                Attendance::updateOrCreate(
                    [
                        'classroom_id' => $classroom->id,
                        'section_id'   => $section->id,
                        'student_id'   => (int)$studentId,
                        'date'         => $date,
                        'subject_id'   => $subjectId,
                    ],
                    [
                        'status'     => $status,
                        'notes'      => $data['notes'] ?? null,
                        'marked_by'  => $teacherId,
                        'teacher_id' => $teacherId,
                    ]
                );
            }
        });

        return redirect()->route('teacher.attendance.details', [$classroom->id, $section->id, $date, $subjectId])
            ->with('success', 'Attendance updated.');
    }

    /* ---------------- PHYSICAL EXAMS & MARKS ---------------- */
  // app/Http/Controllers/YourController.php (replace the existing methods)

public function createPhysicalExam(Classroom $classroom)
{
    $this->authorizeAssignmentAccess($classroom);

    // load teacher assignments (each ties a section -> subject for this teacher & classroom)
    $assignments = TeacherAssignment::where('teacher_id', auth()->id())
        ->where('classroom_id', $classroom->id)
        ->with('section', 'subject')
        ->get();

    return view('teacher.physical_exams.create', compact('classroom', 'assignments'));
}

public function storePhysicalExam(Request $request, Classroom $classroom)
{
    $this->authorizeAssignmentAccess($classroom);

    $data = $request->validate([
        'title' => 'required|string|max:255',
        'exam_date' => 'required|date',
        'total_marks' => 'required|integer|min:1',
        // we now expect the teacher to select a TeacherAssignment (so we know both section & subject)
        'assignment_id' => 'required|exists:teacher_assignments,id',
    ]);

    // load assignment to get section_id and subject_id
    $assignment = TeacherAssignment::findOrFail($data['assignment_id']);

    $exam = PhysicalExam::create([
        'title' => $data['title'],
        'exam_date' => $data['exam_date'],
        'total_marks' => $data['total_marks'],
        'created_by' => auth()->id(),
        'classroom_id' => $classroom->id,
        'section_id' => $assignment->section_id,
        'subject_id' => $assignment->subject_id, // <-- stored here
    ]);

    return redirect()->route('teacher.classrooms.show', $classroom)->with('success', 'Physical exam created. Now add marks.');
}

public function showPhysicalExam(PhysicalExam $exam)
{
    // ensure teacher created it
    if ($exam->created_by != auth()->id()) abort(403);

    // eager load marks and the student for each mark and exam->subject
    $exam->load('marks.student', 'subject', 'classroom');

    // get students of that classroom & section (adjust if your pivot model name differs)
    $students = ClassroomStudent::with('student')
        ->where('classroom_id', $exam->classroom_id)
        ->where('section_id', $exam->section_id)
        ->get();

    return view('teacher.physical_exams.show', compact('exam', 'students'));
}

public function storePhysicalExamMarks(Request $request, PhysicalExam $exam)
{
    if ($exam->created_by != auth()->id()) abort(403);

    $data = $request->validate([
        'marks' => 'required|array',
        'marks.*.student_id' => 'required|exists:users,id',
        'marks.*.marks_obtained' => 'required|numeric|min:0|max:' . $exam->total_marks,
        'marks.*.remarks' => 'nullable|string',
    ]);

    foreach ($data['marks'] as $m) {
        PhysicalExamMark::updateOrCreate(
            ['physical_exam_id' => $exam->id, 'student_id' => $m['student_id']],
            ['marks_obtained' => $m['marks_obtained'], 'remarks' => $m['remarks'] ?? null]
        );
    }

    return back()->with('success', 'Marks saved.');
}

    /* ---------------- NOTIFICATIONS ---------------- */
    public function notifications()
    {
        $notifications = auth()->user()->unreadNotifications()->paginate(10);
        return view('teacher.notifications.index', compact('notifications'));
    }

    public function markNotificationRead($id)
    {
        $n = auth()->user()->notifications()->where('id',$id)->firstOrFail();
        $n->markAsRead();
        return back();
    }

    /* ---------------- TIMETABLE EXPORT ---------------- */
    public function exportTimetablePdf(Classroom $classroom)
    {
        // teacher must be assigned to classroom
        $has = TeacherAssignment::where('teacher_id', auth()->id())
            ->where('classroom_id', $classroom->id)
            ->exists();
        if (!$has) abort(403);

        // load timetable entries for the classroom (and optional section)
        $entries = TimetableEntry::with('subject','teacher')
            ->where('classroom_id', $classroom->id)
            ->orderBy('start_time')
            ->get()
            ->groupBy(function($e){ return Carbon::parse($e->start_time)->format('H:i') . '|' . Carbon::parse($e->end_time)->format('H:i'); });

        // prepare days array like earlier PDF template expects
        $days = [];
        foreach ($entries as $key => $group) {
            foreach ($group as $entry) {
                $dayIndex = (int)Carbon::parse($entry->day)->dayOfWeek; // expects day stored or use integer
                $days[$dayIndex][] = $entry;
            }
        }

        $pdf = PDF::loadView('teacher.timetable_pdf', [
            'classroom' => $classroom,
            'section' => null,
            'days' => $days,
            'slots' => array_keys($entries->toArray()),
        ]);

        return $pdf->download($classroom->slug . '_timetable.pdf');
    }

    /* ---------------- Helpers ---------------- */
    protected function authorizeAssignmentAccess(Classroom $classroom)
    {
        $has = TeacherAssignment::where('teacher_id', auth()->id())
            ->where('classroom_id', $classroom->id)
            ->exists();
        if (!$has) abort(403, 'You are not assigned to this classroom.');
    }
 public function assignmentsIndex(Request $request)
{
    $teacherId = auth()->id();

    // Get classrooms this teacher is associated with.
    // Adjust relation name if your app uses a different relation.
    $classrooms = Classroom::whereHas('teacherAssignments', function($q) use ($teacherId) {
        $q->where('teacher_id', $teacherId);
    })->orderBy('name')->get();

    // Base query for assignments created by this teacher
    $query = Assignment::with(['classroom','section'])
                ->where('created_by', $teacherId);

    // Apply classroom filter if present
    if ($request->filled('classroom_id')) {
        $query->where('classroom_id', $request->classroom_id);
    }

    // Paginate and preserve the classroom_id query param
    $assignments = $query->orderBy('created_at', 'desc')
                         ->paginate(12)
                         ->appends($request->only('classroom_id'));

    return view('teacher.assignments.index', compact('assignments','classrooms'));
}
/**
 * Show teacher's quizzes across all classrooms
 */
public function attendanceIndex(Request $request)
{
    $teacherId = auth()->id();

    // classrooms the teacher is assigned to
    $classrooms = Classroom::whereHas('teacherAssignments', function($q) use ($teacherId) {
        $q->where('teacher_id', $teacherId);
    })->orderBy('name')->get();

    // TeacherAssignment list (used in the attendance view)
    $query = TeacherAssignment::with(['classroom','section','subject'])
        ->where('teacher_id', $teacherId);

    if ($request->filled('classroom_id')) {
        $query->where('classroom_id', $request->classroom_id);
    }

    $assignments = $query->orderBy('classroom_id')->get();

    return view('teacher.attendance.index', compact('assignments','classrooms'));
}

public function quizzesIndex(Request $request)
{
    $teacherId = auth()->id();

    $classrooms = Classroom::whereHas('teacherAssignments', function($q) use ($teacherId) {
        $q->where('teacher_id', $teacherId);
    })->orderBy('name')->get();

    $query = Quiz::with(['classroom','section'])
                 ->where('created_by', $teacherId);

    if ($request->filled('classroom_id')) {
        $query->where('classroom_id', $request->classroom_id);
    }

    $quizzes = $query->orderBy('created_at','desc')
                     ->paginate(12)
                     ->appends($request->only('classroom_id'));

    return view('teacher.quizzes.index', compact('quizzes','classrooms'));
}

/**
 * Chat index — simple list of conversations (you can replace by ChatController later)
 */
public function chatIndex()
{
    $teacherId = auth()->id();
    // stub: you likely have a Message model or conversations — here we return an empty list for now
    $conversations = []; // replace with actual query to your conversations/messages
    return view('teacher.chat.index', compact('conversations'));
}
}
