<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\Classroom;
use App\Models\ClassroomStudent;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\ExamReport;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\QuizAttempt;
use App\Models\Attendance;
use App\Models\PhysicalExam;
use App\Models\PhysicalExamMark;
use App\Models\TeacherAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class StudentDashboardController extends Controller
{
 public function dashboard()
{
    $studentId = auth()->id();

    // student pivots (classroom + section mapping)
    $pivots = ClassroomStudent::where('student_id', $studentId)->get();
    $classroomIds = $pivots->pluck('classroom_id')->unique()->all();
    $sectionIds   = $pivots->pluck('section_id')->unique()->all();

    $classrooms = Classroom::whereIn('id', $classroomIds)->with('sections')->get();

    // 🔹 Upcoming assignments (due in next 7 days, only for student’s sections)
    $assignmentsUpcoming = Assignment::whereIn('classroom_id', $classroomIds)
        ->whereIn('section_id', $sectionIds)
        ->whereBetween('due_at', [now(), now()->addDays(7)])
        ->with('classroom','section')
        ->get()
        ->filter(fn($a) => !$a->submissions()->where('student_id',$studentId)->exists())
        ->values();

    // 🔹 Upcoming quizzes (only published + not attempted, for student’s sections)
    $quizzesUpcoming = Quiz::whereIn('classroom_id', $classroomIds)
        ->whereIn('section_id', $sectionIds)
        ->where('is_published', true)
        ->with('classroom','section','subject')
        ->get()
        ->filter(fn($q) => !QuizAttempt::where('quiz_id',$q->id)->where('student_id',$studentId)->exists())
        ->values();

    // 🔹 Notifications
    $notifications = auth()->user()->unreadNotifications()->take(6)->get();

    // 🔹 Progress Metrics (scoped by classroom + section)
    $totalAssignments = Assignment::whereIn('classroom_id', $classroomIds)
        ->whereIn('section_id', $sectionIds)
        ->count();

    $submitted = AssignmentSubmission::where('student_id', $studentId)->count();

    $assignmentProgress = $totalAssignments
        ? round($submitted / $totalAssignments * 100)
        : 0;

    $totalQuizzes = Quiz::whereIn('classroom_id', $classroomIds)
        ->whereIn('section_id', $sectionIds)
        ->count();

    $attempted = QuizAttempt::where('student_id', $studentId)->count();

    $quizProgress = $totalQuizzes
        ? round($attempted / $totalQuizzes * 100)
        : 0;

    // 🔹 Attendance percentage (scoped by classroom + section)
    $totalDays = Attendance::whereIn('classroom_id', $classroomIds)
        ->whereIn('section_id', $sectionIds)
        ->where('student_id', $studentId)
        ->count();

    $presentDays = Attendance::whereIn('classroom_id', $classroomIds)
        ->whereIn('section_id', $sectionIds)
        ->where('student_id', $studentId)
        ->where('status', 'present')
        ->count();

    $attendancePercent = $totalDays
        ? round($presentDays / $totalDays * 100)
        : 0;

    // 🔹 Random motivational quote
    $quotes = [
        "Education is the passport to the future.",
        "Small daily improvements lead to stunning results.",
        "Practice makes permanent.",
        "The beautiful thing about learning is nobody can take it away from you."
    ];
    $quote = $quotes[array_rand($quotes)];

    return view('student.dashboard', compact(
        'classrooms',
        'assignmentsUpcoming',
        'quizzesUpcoming',
        'notifications',
        'assignmentProgress',
        'quizProgress',
        'attendancePercent',
        'quote'
    ));
}

    /* ---------- Classrooms ---------- */
    public function classroomsIndex()
    {
        $studentId = auth()->id();
        $classrooms = Classroom::whereIn('id', ClassroomStudent::where('student_id',$studentId)->pluck('classroom_id'))->with('sections')->get();
        return view('student.classrooms.index', compact('classrooms'));
    }

public function classroomShow(Classroom $classroom)
{
    $studentId = auth()->id();

    // ✅ Ensure student is enrolled
    $pivot = ClassroomStudent::where('classroom_id', $classroom->id)
        ->where('student_id', $studentId)
        ->firstOrFail();

    $sectionId = $pivot->section_id;

    // ✅ Latest unread notifications (limit 6)
    $notifications = auth()->user()->unreadNotifications()->take(6)->get();

    // ✅ Subjects + teachers for this section
    $subjects = TeacherAssignment::where('classroom_id', $classroom->id)
        ->where('section_id', $sectionId)
        ->with('subject', 'teacher')
        ->get();

    // ✅ Assignments scoped by section
    $assignments = Assignment::with('creator')
        ->where('classroom_id', $classroom->id)
        ->where('section_id', $sectionId)
        ->orderByDesc('due_at')
        ->get()
        ->map(function ($assignment) use ($studentId) {
            $assignment->submitted = $assignment->submissions()
                ->where('student_id', $studentId)
                ->exists();
            return $assignment;
        });

    // ✅ Quizzes scoped by section
    $quizzes = Quiz::with('creator')
        ->where('classroom_id', $classroom->id)
        ->where('section_id', $sectionId)
        ->where('is_published', true)
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($quiz) use ($studentId) {
            $quiz->attempted = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('student_id', $studentId)
                ->exists();
            return $quiz;
        });

    // ✅ Classmates from same section
    $classmates = ClassroomStudent::with('student')
        ->where('classroom_id', $classroom->id)
        ->where('section_id', $sectionId)
        ->get();

    // ✅ Exam reports only for this classroom + section
    $examReports = ExamReport::with(['results' => function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        }])
        ->where('classroom_id', $classroom->id)
        ->orderByDesc('exam_date')
        ->get();

    // ✅ Progress metrics (scoped by classroom + section)
    $totalAssignments = Assignment::where('classroom_id', $classroom->id)
        ->where('section_id', $sectionId)
        ->count();

    $submittedAssignments = AssignmentSubmission::where('student_id', $studentId)
        ->whereIn('assignment_id', Assignment::where('classroom_id', $classroom->id)
            ->where('section_id', $sectionId)
            ->pluck('id'))
        ->count();

    $assignmentProgress = $totalAssignments
        ? round($submittedAssignments / $totalAssignments * 100)
        : 0;

    $totalQuizzes = Quiz::where('classroom_id', $classroom->id)
        ->where('section_id', $sectionId)
        ->count();

    $attemptedQuizzes = QuizAttempt::where('student_id', $studentId)
        ->whereIn('quiz_id', Quiz::where('classroom_id', $classroom->id)
            ->where('section_id', $sectionId)
            ->pluck('id'))
        ->count();

    $quizProgress = $totalQuizzes
        ? round($attemptedQuizzes / $totalQuizzes * 100)
        : 0;

    $totalDays = Attendance::where('classroom_id', $classroom->id)
        ->where('section_id', $sectionId)
        ->where('student_id', $studentId)
        ->count();

    $presentDays = Attendance::where('classroom_id', $classroom->id)
        ->where('section_id', $sectionId)
        ->where('student_id', $studentId)
        ->where('status', 'present')
        ->count();

    $attendancePercent = $totalDays
        ? round($presentDays / $totalDays * 100)
        : 0;

    return view('student.classrooms.show', compact(
        'classroom',
        'sectionId',
        'subjects',
        'assignments',
        'quizzes',
        'classmates',
        'examReports',
        'notifications',
        'assignmentProgress',
        'quizProgress',
        'attendancePercent'
    ));
}


    /* ---------- Assignments ---------- */
  public function assignmentsIndex()
{
    $studentId = auth()->id();

    // find student's classroom + section
    $pivot = ClassroomStudent::where('student_id', $studentId)->first();

    if (!$pivot) {
        abort(403, 'You are not enrolled in any classroom.');
    }

    $classroomId = $pivot->classroom_id;
    $sectionId   = $pivot->section_id;

    // get assignments with pagination
    $assignments = Assignment::where('classroom_id', $classroomId)
        ->where('section_id', $sectionId)
        ->with('classroom', 'section')
        ->orderByDesc('created_at')
        ->paginate(10);

    // append submitted flag for each assignment
    $assignments->getCollection()->transform(function ($assignment) use ($studentId) {
        $assignment->submitted = $assignment->submissions()
            ->where('student_id', $studentId)
            ->exists();
        return $assignment;
    });

    return view('student.assignments.index', compact('assignments'));
}



    public function assignmentShow(Assignment $assignment)
    {
        $studentId = auth()->id();
        // ensure student belongs to the classroom
        $enrolled = ClassroomStudent::where('classroom_id',$assignment->classroom_id)->where('student_id',$studentId)->exists();
        abort_unless($enrolled, 403);

        $submission = $assignment->submissions()->where('student_id', $studentId)->first();

        return view('student.assignments.show', compact('assignment','submission'));
    }

    public function submitAssignment(Request $request, Assignment $assignment)
    {
        $studentId = auth()->id();
        // ensure enrolled
        $enrolled = ClassroomStudent::where('classroom_id',$assignment->classroom_id)->where('student_id',$studentId)->exists();
        abort_unless($enrolled, 403);

        $data = $request->validate([
            'answer_text' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,zip|max:5120',
        ]);

        $files = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $f) {
                $path = $f->store('assignments', 'public'); // requires storage:link
                $files[] = $path;
            }
        }

        $submission = AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $studentId],
            [
                'files' => $files ?: null,
                'answer_text' => $data['answer_text'] ?? null,
                'submitted_at' => now(),
            ]
        );

        return redirect()->route('student.assignments.show', $assignment)->with('success','Assignment submitted.');
    }

    /* ---------- Quizzes ---------- */


// quizzesIndex unchanged except ensure relations are loaded
public function quizzesIndex()
{
    $studentId = auth()->id();

    $pivot = ClassroomStudent::where('student_id', $studentId)->first();

    if (!$pivot) {
        abort(403, 'You are not enrolled in any classroom.');
    }

    $classroomId = $pivot->classroom_id;
    $sectionId   = $pivot->section_id;

    $quizzes = Quiz::with('creator')
        ->where('classroom_id', $classroomId)
        ->where('section_id', $sectionId)
        ->where('is_published', true)
        ->orderByDesc('created_at')
        ->paginate(10);

    return view('student.quizzes.index', compact('quizzes'));
}


public function quizShow(Quiz $quiz)
{
    $studentId = auth()->id();

    // ensure student is enrolled
    abort_unless(
        ClassroomStudent::where('classroom_id', $quiz->classroom_id)
            ->where('student_id', $studentId)->exists(),
        403
    );

    $quiz->load('questions.options');

    // total marks
    $totalMarks = $quiz->questions->sum('marks');

    // student's attempt (may be null)
    $attempt = QuizAttempt::where('quiz_id', $quiz->id)
        ->where('student_id', $studentId)
        ->first();

    // compute remaining seconds (only when attempt exists and not finished)
    $remainingSeconds = null;
    if ($attempt && !$attempt->finished_at) {
        $startedAt = $attempt->started_at ? Carbon::parse($attempt->started_at) : Carbon::parse($attempt->created_at);
        $allowedEnd = $startedAt->copy()->addMinutes($quiz->duration_minutes ?? 0);
        $remainingSeconds = max(0, Carbon::now()->diffInSeconds($allowedEnd));
    }

    return view('student.quizzes.show', compact('quiz', 'attempt', 'totalMarks', 'remainingSeconds'));
}

public function startQuizAttempt(Quiz $quiz)
{
    $studentId = auth()->id();

    // ensure student is enrolled
    abort_unless(
        ClassroomStudent::where('classroom_id', $quiz->classroom_id)
            ->where('student_id', $studentId)->exists(),
        403
    );

    // Create attempt if not exists (record started_at)
    $attempt = QuizAttempt::firstOrCreate(
        ['quiz_id' => $quiz->id, 'student_id' => $studentId],
        ['started_at' => Carbon::now()]
    );

    // If already finished, inform user
    if ($attempt->finished_at) {
        return redirect()->route('student.quizzes.show', $quiz)
            ->with('info', 'You already completed this quiz.');
    }

    return redirect()->route('student.quizzes.show', $quiz)->with('success', 'Quiz started.');
}

public function submitQuizAttempt(Request $request, Quiz $quiz)
{
    $studentId = auth()->id();

    // ensure student is enrolled
    abort_unless(
        ClassroomStudent::where('classroom_id', $quiz->classroom_id)
            ->where('student_id', $studentId)->exists(),
        403
    );

    $quiz->load('questions.options');

    // Find or create attempt (if user submitted without starting)
    $attempt = QuizAttempt::firstOrCreate(
        ['quiz_id' => $quiz->id, 'student_id' => $studentId],
        ['started_at' => Carbon::now()]
    );

    // If already finished, don't re-grade
    if ($attempt->finished_at) {
        return redirect()->route('student.quizzes.show', $quiz)
            ->with('info', 'This quiz attempt is already finished.');
    }

    // determine allowed end time and whether time is over
    $startedAt = $attempt->started_at ? Carbon::parse($attempt->started_at) : Carbon::now();
    $allowedEnd = $startedAt->copy()->addMinutes($quiz->duration_minutes ?? 0);
    $now = Carbon::now();
    $timeOver = $now->greaterThan($allowedEnd);

    // Accept answers array optionally. We'll normalize below.
    $data = $request->validate([
        'answers' => 'nullable|array',
        'answers.*.question_id' => 'required_with:answers|exists:quiz_questions,id',
        'answers.*.option_id' => 'nullable|exists:quiz_options,id',
    ]);

    // Build a canonical answers array keyed to quiz questions.
    $submittedAnswers = [];

    // If input provided, try to map the provided answers to questions; otherwise create empty answers.
    $provided = $data['answers'] ?? [];

    foreach ($quiz->questions as $question) {
        $qId = $question->id;
        $found = null;

        // Provided structure may be numeric-indexed or keyed by question id; handle both.
        foreach ($provided as $key => $ans) {
            // if ans contains question_id and matches
            if (isset($ans['question_id']) && (int)$ans['question_id'] === (int)$qId) {
                $found = $ans;
                break;
            }
            // or if the outer key equals question id -> $provided[$qId]
            if ((string)$key === (string)$qId) {
                $found = $ans;
                break;
            }
        }

        $submittedAnswers[] = [
            'question_id' => $qId,
            'option_id' => isset($found['option_id']) ? (int)$found['option_id'] : null,
        ];
    }

    // Grade
    $score = 0;
    $totalMarks = $quiz->questions->sum('marks');

    foreach ($submittedAnswers as $ans) {
        $q = $quiz->questions->where('id', $ans['question_id'])->first();
        if (!$q) continue;

        $selectedOptionId = $ans['option_id'] ?? null;

        $correctIds = $q->options->where('is_correct', true)->pluck('id')->toArray();
        if ($selectedOptionId !== null && in_array($selectedOptionId, $correctIds, true)) {
            $score += ($q->marks ?? 1);
        }
    }

    $percentage = $totalMarks > 0 ? round(($score / $totalMarks) * 100, 2) : 0;

    // if time over, set finished_at to allowedEnd; otherwise now
    $finishedAt = $timeOver ? $allowedEnd : $now;

    // Save attempt
    $attempt->update([
        'finished_at' => $finishedAt,
        'answers' => $submittedAnswers,
        'score' => $score,
        'percentage' => $percentage,
    ]);

    return redirect()->route('student.quizzes.show', $quiz)
        ->with('success', "Quiz submitted. You scored $score / $totalMarks ($percentage%).");
}

    /* ---------- Attendance ---------- */
// in App\Http\Controllers\Student\StudentDashboardController
public function attendanceIndex()
{
    $studentId = auth()->id();

    // classrooms the student is enrolled in
    $pivots = ClassroomStudent::where('student_id', $studentId)
        ->with('classroom.sections')
        ->get();

    $classrooms = $pivots->map(function ($pivot) use ($studentId) {
        $classroom = $pivot->classroom;
        $sectionId = $pivot->section_id;

        // per-classroom attendance
        $total = Attendance::where('student_id', $studentId)
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $sectionId)
            ->count();

        $present = Attendance::where('student_id', $studentId)
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $sectionId)
            ->where('status', 'present')
            ->count();

        $classroom->attendance_total = $total;
        $classroom->attendance_present = $present;
        $classroom->attendance_percent = $total ? round($present / $total * 100) : 0;
        $classroom->section_id = $sectionId;

        return $classroom;
    });

    // overall attendance across all classrooms
    $totalOverall = Attendance::where('student_id', $studentId)->count();
    $presentOverall = Attendance::where('student_id', $studentId)
        ->where('status', 'present')
        ->count();
    $overallPercent = $totalOverall ? round($presentOverall / $totalOverall * 100) : 0;

    return view('student.attendance.index', [
        'classrooms' => $classrooms,
        'overallPercent' => $overallPercent,
    ]);
}


/**
 * Attendance detail for a specific classroom + section.
 * Optional query param: subject_id to filter by subject.
 */
public function attendanceDetail(Request $request, Classroom $classroom, Section $section)
{
    $studentId = auth()->id();
    $subjectId = $request->query('subject_id'); // optional filter

    // Ensure student is enrolled in this section
    $enrolled = ClassroomStudent::where([
        'classroom_id' => $classroom->id,
        'section_id'   => $section->id,
        'student_id'   => $studentId,
    ])->exists();

    abort_unless($enrolled, 403, 'Not enrolled in this section');

    // Fetch teacher assignments (subjects + teachers for this classroom/section)
    $assignments = TeacherAssignment::where('classroom_id', $classroom->id)
        ->where('section_id', $section->id)
        ->with(['subject:id,name', 'teacher:id,name'])
        ->get();

    // Build subjects list
    $subjects = $assignments->groupBy('subject_id')->map(function ($group, $subjectId) {
        $subject = $group->first()->subject;
        $teachers = $group->map(fn($a) => [
            'id'   => $a->teacher_id,
            'name' => optional($a->teacher)->name,
        ])->unique('id')->values();

        return [
            'id'       => (int)$subjectId,
            'name'     => $subject?->name ?? 'Subject '.$subjectId,
            'teachers' => $teachers,
        ];
    })->values();

    // Attendance query (records + overall stats)
    $query = Attendance::where([
            'classroom_id' => $classroom->id,
            'section_id'   => $section->id,
            'student_id'   => $studentId,
        ])
        ->with(['subject:id,name', 'teacher:id,name'])
        ->orderByDesc('date');

    if ($subjectId) {
        $query->where('subject_id', $subjectId);
    }

    $records = $query->paginate(30)->appends($request->query());

    // Overall percent for this class/section
    $totalClass   = Attendance::where([
                        'classroom_id' => $classroom->id,
                        'section_id'   => $section->id,
                        'student_id'   => $studentId,
                    ])->count();

    $presentClass = Attendance::where([
                        'classroom_id' => $classroom->id,
                        'section_id'   => $section->id,
                        'student_id'   => $studentId,
                    ])->where('status', 'present')->count();

    $percent = $totalClass ? round(($presentClass / $totalClass) * 100) : 0;

    // Per-subject stats (one query per subject, unavoidable unless you pre-aggregate)
    $subjectStats = $subjects->map(function ($s) use ($classroom, $section, $studentId) {
        $sid = $s['id'];

        $tot = Attendance::where([
                    'classroom_id' => $classroom->id,
                    'section_id'   => $section->id,
                    'student_id'   => $studentId,
                    'subject_id'   => $sid,
                ])->count();

        $p = Attendance::where([
                    'classroom_id' => $classroom->id,
                    'section_id'   => $section->id,
                    'student_id'   => $studentId,
                    'subject_id'   => $sid,
                ])->where('status', 'present')->count();

        return (object)[
            'subject_id'   => $sid,
            'subject_name' => $s['name'],
            'total'        => $tot,
            'present'      => $p,
            'percent'      => $tot ? round(($p / $tot) * 100) : 0,
            'teachers'     => $s['teachers'],
        ];
    });

    return view('student.attendance.detail', compact(
        'classroom', 'section', 'records', 'percent',
        'subjects', 'subjectStats', 'subjectId'
    ));
}

    /* ---------- Marks ---------- */
public function marksIndex()
{
    $studentId = auth()->id();

    // Get all classrooms + sections where this student is enrolled
    $pivots = ClassroomStudent::where('student_id', $studentId)->get();

    if ($pivots->isEmpty()) {
        abort(403, 'Not enrolled in any classroom.');
    }

    $classroomIds = $pivots->pluck('classroom_id');
    $sectionIds   = $pivots->pluck('section_id');

    // Fetch exams for those classrooms/sections
    $exams = PhysicalExam::whereIn('classroom_id', $classroomIds)
        ->whereIn('section_id', $sectionIds)
        ->with(['classroom', 'section', 'subject']) // ✅ load subject from exam
        ->orderByDesc('exam_date')
        ->get();

    // Attach this student's mark for each exam
    $exams->each(function ($exam) use ($studentId) {
        $exam->myMark = $exam->marks()
            ->where('student_id', $studentId)
            ->first(); // each exam has only 1 mark per student
    });

    return view('student.marks.index', compact('exams'));
}


public function marksExam(PhysicalExam $exam)
{
    $studentId = auth()->id();

    // Find this student's mark for this exam
    $mark = PhysicalExamMark::where('physical_exam_id', $exam->id)
        ->where('student_id', $studentId)
        ->first();

    return view('student.marks.show', compact('exam','mark'));
}

public function destroy(PhysicalExam $exam)
{
    // Delete related marks first
    $exam->marks()->delete();

    // Delete the exam itself
    $exam->delete();

    return redirect()->back()->with('success', 'Exam deleted successfully.');
}

    /* ---------- Notifications ---------- */
    public function notifications()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('student.notifications.index', compact('notifications'));
    }

    public function markNotificationRead($id)
    {
        $n = auth()->user()->notifications()->where('id',$id)->firstOrFail();
        $n->markAsRead();
        return back();
    }
}
