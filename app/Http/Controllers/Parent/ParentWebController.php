<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\QuizAttempt;
use App\Models\PhysicalExamMark;
use App\Models\ExamReport;
use App\Models\ExamResult;
use App\Models\TeacherAssignment;
use App\Models\Chat;
use App\Models\Classroom;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ParentWebController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 1. List children
    public function index()
    {
        $parent = Auth::user();
        abort_unless($parent->isParent(), 403);

        $children = $parent->children()->with('classrooms')->get();

        return view('parent.children.index', compact('children'));
    }

    // 2. Overview / summary for selected child

public function overview(User $child)
{
    $this->authorizeChild($child);

    // Load classrooms with pivot data (section_id, roll_no)
    $classrooms = $child->classrooms()->withPivot('section_id','roll_no')->get();

    // collect classroom ids & section ids for summary queries
    $classroomIds = $classrooms->pluck('id')->toArray();
    $sectionIds = $classrooms->map(function ($c) {
        return $c->pivot->section_id ?? null;
    })->filter()->unique()->values()->toArray();

    // --- Overall summary (assignments/quizzes/physical/exams/attendance) ---
    // Assignments relevant to student's classrooms & sections (include whole-class assignments section_id = NULL)
    $assignmentsQuery = Assignment::whereIn('classroom_id', $classroomIds)
        ->when(count($sectionIds) > 0, function ($q) use ($sectionIds) {
            $q->where(function ($qq) use ($sectionIds) {
                $qq->whereNull('section_id')->orWhereIn('section_id', $sectionIds);
            });
        }, function ($q) {
            $q->whereNull('section_id');
        });

    $assignmentIds = $assignmentsQuery->pluck('id');
    $totalAssignments = $assignmentIds->count();

    $submittedCount = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
        ->where('student_id', $child->id)
        ->count();

    $avgAssignmentScore = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
        ->where('student_id', $child->id)
        ->whereNotNull('marks_awarded')
        ->avg('marks_awarded');

    // Quizzes (student attempts)
    $quizAttempts = QuizAttempt::where('student_id', $child->id)->get();
    $quizAttemptsCount = $quizAttempts->count();
    $quizAvgScore = $quizAttempts->whereNotNull('score')->avg('score');

    // Physical exams
    $physicalMarks = PhysicalExamMark::where('student_id', $child->id)->get();
    $physicalAvg = $physicalMarks->avg('marks_obtained');

    // Exam results
    $examResults = ExamResult::where('student_id', $child->id)->with('exam')->get();
    $examAvgPercent = $examResults->avg(function ($r) {
        if ($r->total_marks && $r->total_marks > 0) {
            return ($r->marks_obtained / $r->total_marks) * 100;
        }
        return null;
    });

    // Attendance last 30 days
    $to = Carbon::now();
    $from = (clone $to)->subDays(30);
    $attRecords = Attendance::where('student_id', $child->id)
        ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
        ->get();

    $presentCount = $attRecords->where('status', 'present')->count();
    $totalDays = max(1, $attRecords->count());
    $attendancePct = round(($presentCount / $totalDays) * 100, 2);

    $summary = [
        'assignments' => [
            'total' => $totalAssignments,
            'submitted' => $submittedCount,
            'average_marks' => $avgAssignmentScore ? round($avgAssignmentScore, 2) : null,
        ],
        'quizzes' => [
            'attempts' => $quizAttemptsCount,
            'average_score' => $quizAvgScore ? round($quizAvgScore, 2) : null,
        ],
        'physical_exams' => [
            'count' => $physicalMarks->count(),
            'average_marks' => $physicalAvg ? round($physicalAvg, 2) : null,
        ],
        'exams' => [
            'count' => $examResults->count(),
            'average_percent' => $examAvgPercent ? round($examAvgPercent, 2) : null,
        ],
        'attendance' => [
            'percent_last_30_days' => $attendancePct,
            'present_last_30' => $presentCount,
            'total_days_last_30' => $totalDays,
        ],
    ];

    // --- Per-classroom / section breakdown (subjects + teachers + counts + recent items) ---
    $classroomDetails = [];

    foreach ($classrooms as $classroom) {
        $sectionId = $classroom->pivot->section_id ?? null;
        $rollNo = $classroom->pivot->roll_no ?? null;
        $section = $sectionId ? Section::find($sectionId) : null;

        // subjects assigned to this classroom for this section (TeacherAssignment includes teacher and subject)
        $subjects = TeacherAssignment::where('classroom_id', $classroom->id)
            ->when($sectionId, function ($q) use ($sectionId) {
                $q->where('section_id', $sectionId);
            })
            ->with(['subject', 'teacher'])
            ->get();

        // assignments for this classroom+section (include whole-class (NULL) and section-specific)
        $assignmentsForSection = Assignment::where('classroom_id', $classroom->id)
            ->when($sectionId, function ($q) use ($sectionId) {
                $q->where(function ($qq) use ($sectionId) {
                    $qq->whereNull('section_id')->orWhere('section_id', $sectionId);
                });
            }, function ($q) {
                $q->whereNull('section_id');
            })
            ->with('subject')
            ->latest()
            ->get();

        // submissions of this student for the above assignments
        $assignmentSubmissions = AssignmentSubmission::whereIn('assignment_id', $assignmentsForSection->pluck('id')->toArray())
            ->where('student_id', $child->id)
            ->get()
            ->keyBy('assignment_id');

        // quizzes available for this classroom and section (include whole-class quizzes)
        $quizzesForSection = Quiz::where('classroom_id', $classroom->id)
            ->when($sectionId, function ($q) use ($sectionId) {
                $q->where(function ($qq) use ($sectionId) {
                    $qq->whereNull('section_id')->orWhere('section_id', $sectionId);
                });
            }, function ($q) {
                $q->whereNull('section_id');
            })
            ->with('subject')
            ->latest()
            ->get();

        // student's attempts for quizzes in this classroom
        $quizAttemptsForSection = QuizAttempt::where('student_id', $child->id)
            ->whereIn('quiz_id', $quizzesForSection->pluck('id')->toArray())
            ->with('quiz')
            ->get()
            ->keyBy('quiz_id');

        $classroomDetails[] = [
            'classroom' => $classroom,
            'roll_no' => $rollNo,
            'section' => $section,
            'subjects' => $subjects,
            'assignments' => $assignmentsForSection,
            'assignment_submissions' => $assignmentSubmissions,
            'quizzes' => $quizzesForSection,
            'quiz_attempts' => $quizAttemptsForSection,
        ];
    }

    // recent lists filtered to child's sections (combine across classrooms)
    $recentAssignments = Assignment::whereIn('classroom_id', $classroomIds)
        ->when(count($sectionIds) > 0, function ($q) use ($sectionIds) {
            $q->where(function ($qq) use ($sectionIds) {
                $qq->whereNull('section_id')->orWhereIn('section_id', $sectionIds);
            });
        }, function ($q) {
            $q->whereNull('section_id');
        })
        ->with('subject')
        ->latest()
        ->limit(6)
        ->get();

    $recentQuizAttempts = QuizAttempt::where('student_id', $child->id)
        ->with('quiz.subject')
        ->latest()
        ->limit(6)
        ->get();

    return view('parent.child.overview', compact('child', 'summary', 'classroomDetails', 'recentAssignments', 'recentQuizAttempts'));
}


    // 3. Attendance view (with optional date range)
    public function attendance(Request $request, User $child)
    {
        $this->authorizeChild($child);

        $from = $request->input('from') ? Carbon::parse($request->input('from'))->toDateString() : Carbon::now()->subMonths(1)->toDateString();
        $to   = $request->input('to')   ? Carbon::parse($request->input('to'))->toDateString()   : Carbon::now()->toDateString();

        // Attempt to eager load subject if relation exists (optional)
        $query = Attendance::where('student_id', $child->id)
            ->whereBetween('date', [$from, $to])
            ->orderByDesc('date');

        // if subject_id is present in table, developers should add relation 'subject' to Attendance model.
        $records = $query->get()->groupBy(function ($rec) {
            return $rec->date->toDateString();
        });

        // Basic stats
        $presentCount = Attendance::where('student_id', $child->id)
            ->whereBetween('date', [$from, $to])
            ->where('status', 'present')
            ->count();

        $totalDays = Attendance::where('student_id', $child->id)
            ->whereBetween('date', [$from, $to])
            ->count();

        $attendancePct = $totalDays ? round(($presentCount / $totalDays) * 100, 2) : null;

        return view('parent.child.attendance', compact('child', 'records', 'from', 'to', 'presentCount', 'totalDays', 'attendancePct'));
    }

    // 4. Assignments list
  public function assignments(Request $request, User $child)
{
    $this->authorizeChild($child);

    // eager-load classrooms so pluck uses the collection (no ambiguous SQL)
    $child->load('classrooms');

    $classroomIds = $child->classrooms->pluck('id')->toArray();

    $sectionIds = $child->classrooms
        ->map(fn($c) => $c->pivot->section_id ?? null)
        ->filter()
        ->unique()
        ->values()
        ->toArray();

    $query = Assignment::whereIn('classroom_id', $classroomIds)
        ->with('subject', 'creator')
        ->latest();

    if (count($sectionIds) > 0) {
        $query->where(function ($q) use ($sectionIds) {
            $q->whereNull('section_id')->orWhereIn('section_id', $sectionIds);
        });
    } else {
        $query->whereNull('section_id');
    }

    $assignments = $query->paginate(20);

    // For quick status per assignment (only checks current page)
    $assignmentIds = $assignments->pluck('id')->toArray();
    $submissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
        ->where('student_id', $child->id)
        ->get()
        ->keyBy('assignment_id');

    return view('parent.child.assignments', compact('child', 'assignments', 'submissions'));
}

    // 5. Quizzes list
   public function quizzes(Request $request, User $child)
{
    $this->authorizeChild($child);

    $attempts = QuizAttempt::where('student_id', $child->id)
        ->with(['quiz.subject', 'quiz.questions']) // eager load subject & questions
        ->latest()
        ->paginate(20);

    // Add total_marks attribute per attempt
    $attempts->getCollection()->transform(function ($attempt) {
        $attempt->total_marks = $attempt->quiz
            ? $attempt->quiz->questions->sum('marks')
            : 0;
        return $attempt;
    });

    return view('parent.child.quizzes', compact('child', 'attempts'));
}

    // 6. Physical exams
    public function physicalExams(Request $request, User $child)
    {
        $this->authorizeChild($child);

        $marks = PhysicalExamMark::where('student_id', $child->id)
            ->with('exam')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('parent.child.physical_exams', compact('child', 'marks'));
    }
 public function chatIndex()
    {
        $parent = Auth::user();
        abort_unless($parent->isParent(), 403);

        $children = $parent->children()->with('classrooms')->get();

        return view('parent.chat.index', compact('children'));
    }

    /**
     * Show teachers in a classroom + chat window
     */
    public function chatClassroom(Request $request, Classroom $classroom)
    {
        $me = $request->user();
        abort_unless($me->isParent(), 403);

        // Ensure this parent has at least one child in this classroom
        $hasChild = $me->children()->whereHas('classrooms', fn($q) => $q->where('classroom_id', $classroom->id))->exists();
        abort_unless($hasChild, 403);

        // Get teachers assigned in this classroom (with their subjects)
        $teachers = User::whereHas('teacherAssignments', fn($q) => $q->where('classroom_id', $classroom->id))
            ->with([
                'teacherAssignments' => fn($q) =>
                    $q->where('classroom_id', $classroom->id)->with('subject')
            ])
            ->select('users.id','users.name')
            ->get();

        // If parent clicked a teacher
        $activeTeacher = null;
        $activeChat = null;
        $messages = collect();

        if ($teacherId = $request->query('user')) {
            $teacher = User::find($teacherId);

            if ($teacher) {
                $activeTeacher = $teacher;

                // Find or create chat
                $chat = Chat::where('type', 'private')
                    ->where('classroom_id', $classroom->id)
                    ->whereHas('participants', fn($q) => $q->where('user_id', $me->id))
                    ->whereHas('participants', fn($q) => $q->where('user_id', $teacher->id))
                    ->first();

                if (! $chat) {
                    DB::beginTransaction();
                    try {
                        $chat = Chat::create([
                            'classroom_id' => $classroom->id,
                            'created_by'   => $me->id,
                            'type'         => 'private',
                            'subject'      => null,
                        ]);

                        $chat->participants()->createMany([
                            ['user_id' => $me->id,      'role' => $me->role],
                            ['user_id' => $teacher->id, 'role' => $teacher->role],
                        ]);

                        DB::commit();
                    } catch (\Throwable $e) {
                        DB::rollBack();
                        throw $e;
                    }
                }

                $messages = $chat->messages()
                    ->with('sender:id,name')
                    ->orderBy('created_at', 'asc')
                    ->get();

                $activeChat = $chat;
            }
        }

        return view('parent.chat.classroom', compact('classroom','teachers','activeTeacher','activeChat','messages'));
    }

    // 7. Exams list
public function exams(Request $request, User $child)
{
    $this->authorizeChild($child);

    // load classrooms (so we can access pivot data without ambiguous SQL)
    $child->load('classrooms');

    // classroom IDs the child is enrolled in
    $classroomIds = $child->classrooms->pluck('id')->toArray();

    // fetch exam reports for those classrooms (paginated)
    $exams = ExamReport::whereIn('classroom_id', $classroomIds)
        ->latest()
        ->paginate(20);

    // fetch this student's results for the exams we just loaded
    $examIds = $exams->pluck('id')->toArray();
    $myResults = ExamResult::whereIn('exam_report_id', $examIds)
        ->where('student_id', $child->id)
        ->with('subject')
        ->get()
        ->groupBy('exam_report_id');

    // load all teacher assignments (subjects) for these classrooms in one query
    $teacherAssignments = TeacherAssignment::whereIn('classroom_id', $classroomIds)
        ->get(['classroom_id', 'section_id', 'subject_id']);

    // compute number of applicable subjects per exam (based on classroom + child's section for that classroom)
    $subjectCounts = [];
    foreach ($exams as $exam) {
        $classroomId = $exam->classroom_id;

        // find the child's section_id for this specific classroom from the pivot (if any)
        $classroomPivot = $child->classrooms->firstWhere('id', $classroomId);
        $sectionId = $classroomPivot ? ($classroomPivot->pivot->section_id ?? null) : null;

        // select assignments for this classroom, and include:
        //  - assignments that are whole-class (section_id == null)
        //  - assignments specifically for this student's section (section_id == $sectionId)
        $applicable = $teacherAssignments->where('classroom_id', $classroomId)
            ->filter(function ($ta) use ($sectionId) {
                // if TA has null section => whole-class subject (include)
                if ($ta->section_id === null) return true;
                // otherwise include only if matches the student's section
                return $ta->section_id == $sectionId;
            });

        // count distinct subjects (subject_id) — this is the number of subjects for this classroom/section
        $subjectCounts[$exam->id] = $applicable->pluck('subject_id')->unique()->count();
    }

    return view('parent.child.exams', compact('child', 'exams', 'myResults', 'subjectCounts'));
}





    // 8. Exam show (subject-wise result card)
   public function examShow(User $child, ExamReport $exam)
{
    $this->authorizeChild($child);

    // Explicitly pluck classroom IDs
    $classroomIds = $child->classrooms()->pluck('classrooms.id')->toArray();

    // Check if exam belongs to child's classroom(s)
    if (! in_array($exam->classroom_id, $classroomIds)) {
        abort(403);
    }

    $results = ExamResult::where('exam_report_id', $exam->id)
        ->where('student_id', $child->id)
        ->with('subject')
        ->get();

    // compute totals
    $totalObtained = $results->sum('marks_obtained');
    $totalPossible = $results->sum('total_marks');
    $percent = $totalPossible ? round(($totalObtained / $totalPossible) * 100, 2) : null;

    // simple grade mapping (customize as needed)
    $grade = null;
    if ($percent !== null) {
        if ($percent >= 90) {
            $grade = 'A+';
        } elseif ($percent >= 80) {
            $grade = 'A';
        } elseif ($percent >= 70) {
            $grade = 'B';
        } elseif ($percent >= 60) {
            $grade = 'C';
        } else {
            $grade = 'D';
        }
    }

    return view('parent.child.exam_show', compact(
        'child',
        'exam',
        'results',
        'totalObtained',
        'totalPossible',
        'percent',
        'grade'
    ));
}


    // helper to ensure the child belongs to the logged in parent
    protected function authorizeChild(User $child)
    {
        $parent = Auth::user();
        if (! $parent->isParent()) {
            abort(403);
        }

        // support both parent_id or pivot guardians later
        if ($child->parent_id !== $parent->id) {
            abort(403);
        }
    }
}
