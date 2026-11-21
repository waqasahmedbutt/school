<?php

namespace App\Http\Controllers;

use App\Models\ExamReport;
use App\Models\ExamResult;
use App\Models\Classroom;
use App\Models\ClassroomStudent;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\Subject;

use Barryvdh\DomPDF\Facade\Pdf;

class ExamReportController extends Controller
{
    /**
     * Admin: List all exams
     */
    public function index()
    {
        $exams = ExamReport::with('classroom')->latest()->paginate(15);
        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Admin: Show create exam form
     */
    public function create()
    {
        $classrooms = Classroom::all();
        return view('admin.exams.create', compact('classrooms'));
    }

    /**
     * Admin: Store new exam and auto-generate results
     */
// Admin: Store new exam and auto-generate results (section-aware)
public function store(Request $request)
{
    $data = $request->validate([
        'classroom_id' => 'required|exists:classrooms,id',
        'title'        => 'required|string|max:255',
        'description'  => 'nullable|string',
        'exam_date'    => 'nullable|date',
    ]);

    $exam = ExamReport::create($data);

    // Generate empty results for each student × subjects assigned to that student's section
    $classroomStudents = ClassroomStudent::where('classroom_id', $exam->classroom_id)->get();

    foreach ($classroomStudents as $cs) {
        $studentId = $cs->student_id;
        $sectionId = $cs->section_id;

        // subjects assigned for this classroom + student's section
        $subjectIds = TeacherAssignment::where('classroom_id', $exam->classroom_id)
            ->where('section_id', $sectionId)
            ->pluck('subject_id')
            ->unique();

        // if no assignments found for this section, fallback to classroom-wide subjects (optional)
        if ($subjectIds->isEmpty()) {
            $subjectIds = TeacherAssignment::where('classroom_id', $exam->classroom_id)->pluck('subject_id')->unique();
        }

        foreach ($subjectIds as $subjectId) {
            ExamResult::firstOrCreate(
                [
                    'exam_report_id' => $exam->id,
                    'student_id'     => $studentId,
                    'subject_id'     => $subjectId,
                ],
                [
                    // default full marks (teacher can update when entering marks)
                    'total_marks'    => 100,
                    'marks_obtained' => null,
                ]
            );
        }
    }

    return redirect()->route('admin.exams.index')->with('success', 'Exam created successfully.');
}

/**
 * Teacher: Enter marks for students in their subject (section-aware)
 * Accepts optional ?section_id= query param. If not provided, it will pick the first section for this classroom.
 */
public function enterMarks($examId, $subjectId, Request $request)
{
    $exam = ExamReport::with('classroom')->findOrFail($examId);
    $sectionId = $request->query('section_id');

    $students = ClassroomStudent::with('student')
        ->where('classroom_id', $exam->classroom_id)
        ->where('section_id', $sectionId)
        ->get();

    $results = ExamResult::where('exam_report_id', $examId)
        ->where('subject_id', $subjectId)
        ->where('section_id', $sectionId)
        ->get();

    $subject = Subject::find($subjectId);

    return view('teacher.exams.enter-marks', compact('exam', 'results', 'students', 'subjectId', 'sectionId', 'subject'));
}

/**
 * Teacher: Save marks (section-aware)
 */
public function saveMarks(Request $request, ExamReport $exam, $subjectId)
{
    $data = $request->validate([
        'section_id'  => 'required|exists:sections,id',
        'total_marks' => 'required|integer|min:1',
        'marks'       => 'required|array',
        'marks.*'     => 'nullable|integer|min:0',
    ]);

    $sectionId = $data['section_id'];
    $totalMarks = $data['total_marks'];
    $marksArr = $data['marks'];

    // fetch student ids for that classroom+section to ensure teacher cannot update others
    $allowedStudentIds = ClassroomStudent::where('classroom_id', $exam->classroom_id)
        ->where('section_id', $sectionId)
        ->pluck('student_id')
        ->toArray();

    foreach ($marksArr as $studentIdStr => $marks) {
        $studentId = (int)$studentIdStr;

        // skip any student not in allowed list (safety)
        if (!in_array($studentId, $allowedStudentIds, true)) continue;

        ExamResult::updateOrCreate(
            [
                'exam_report_id' => $exam->id,
                'subject_id'     => $subjectId,
                'student_id'     => $studentId,
            ],
            [
                'marks_obtained' => $marks,
                'total_marks'    => $totalMarks,
            ]
        );
    }

    return back()->with('success', 'Marks saved successfully.');
}



    /**
     * Student: View result card
     */
public function resultCard(ExamReport $exam)
{
    $student = auth()->user();

    // Get student's pivot (section_id & roll_no)
    $pivot = $exam->classroom
        ->students()
        ->where('student_id', $student->id)
        ->first();

    $section = $pivot ? Section::find($pivot->pivot->section_id) : null;
    $rollNo  = $pivot ? $pivot->pivot->roll_no : null;

    // Results for this exam
    $results = ExamResult::with('subject')
        ->where('exam_report_id', $exam->id)
        ->where('student_id', $student->id)
        ->get();

    $totalObtained = $results->sum('marks_obtained');
    $totalMarks    = $results->sum('total_marks');
    $percentage    = $totalMarks > 0 ? round(($totalObtained / $totalMarks) * 100, 2) : 0;

    return view('student.exams.result-card', compact(
        'exam',
        'student',
        'results',
        'section',
        'rollNo',
        'totalObtained',
        'totalMarks',
        'percentage'
    ));
}



/**
 * Student: Download result card PDF
 */
public function downloadPdf(ExamReport $exam, User $student)
{
    // Get student's pivot (section_id & roll_no)
  $pivot = ClassroomStudent::where('classroom_id', $exam->classroom_id)
    ->where('student_id', $student->id)
    ->first();

$section = $pivot?->section; // uses relationship from ClassroomStudent
$rollNo  = $pivot?->roll_no;

    // Results
    $results = ExamResult::with('subject')
        ->where('exam_report_id', $exam->id)
        ->where('student_id', $student->id)
        ->get();

    $totalObtained = $results->sum('marks_obtained');
    $totalMarks    = $results->sum('total_marks');
    $percentage    = $totalMarks > 0 ? round(($totalObtained / $totalMarks) * 100, 2) : 0;

    $pdf = PDF::loadView('student.exams.pdf', compact(
        'exam',
        'student',
        'results',
        'section',
        'rollNo',
        'totalObtained',
        'totalMarks',
        'percentage'
    ));

    return $pdf->download("{$student->name}_{$exam->title}_Result.pdf");
}
// Admin: show exam details
public function adminShow(ExamReport $exam, Request $request)
{
    $exam->load('classroom');

    // Get sections for this classroom
    $sections = ClassroomStudent::where('classroom_id', $exam->classroom_id)
        ->with('section')
        ->get()
        ->pluck('section')
        ->unique('id')
        ->values();

    // Get selected section (default first)
    $sectionId = $request->query('section_id', $sections->first()?->id);

    // Students for selected section
    $students = ClassroomStudent::with('student')
        ->where('classroom_id', $exam->classroom_id)
        ->where('section_id', $sectionId)
        ->get();

    return view('admin.exams.show', compact('exam', 'sections', 'sectionId', 'students'));
}

public function studentExamResult(User $student, ExamReport $exam)
{
    $exam->load('classroom');

    $pivot = ClassroomStudent::where('classroom_id', $exam->classroom_id)
        ->where('student_id', $student->id)
        ->first();

    $section = $pivot?->section;
    $rollNo  = $pivot?->roll_no;

    $results = ExamResult::with('subject')
        ->where('exam_report_id', $exam->id)
        ->where('student_id', $student->id)
        ->get();

    $totalObtained = $results->sum('marks_obtained');
    $totalMarks    = $results->sum('total_marks');
    $percentage    = $totalMarks > 0 ? round(($totalObtained / $totalMarks) * 100, 2) : 0;

    return view('admin.students.exam-result', compact(
        'student', 'exam', 'section', 'rollNo', 'results', 'totalObtained', 'totalMarks', 'percentage'
    ));
}


// Teacher: list exams relevant to teacher (classrooms they teach in)
public function teacherIndex()
{
    $teacherId = auth()->id();
    $classroomIds = TeacherAssignment::where('teacher_id', $teacherId)->pluck('classroom_id')->unique()->toArray();

    $exams = ExamReport::whereIn('classroom_id', $classroomIds)
        ->with('classroom')
        ->orderByDesc('exam_date')
        ->paginate(12);

    return view('teacher.exams.index', compact('exams'));
}

// Teacher: show a single exam (only if teacher assigned to that classroom)
public function teacherShow(ExamReport $exam)
{
    $teacherId = auth()->id();
    $assigned = TeacherAssignment::where('teacher_id', $teacherId)
        ->where('classroom_id', $exam->classroom_id)
        ->exists();

    abort_unless($assigned, 403);

    $mySubjects = TeacherAssignment::with(['subject','section'])
        ->where('teacher_id', $teacherId)
        ->where('classroom_id', $exam->classroom_id)
        ->get()
        ->filter(fn($ta) => $ta && $ta->subject && $ta->section)
        ->values();

    $students = ClassroomStudent::with('student')
        ->where('classroom_id', $exam->classroom_id)
        ->get();

    return view('teacher.exams.show', compact('exam','mySubjects','students'));
}

// Student: list exams for student's classrooms
public function studentIndex()
{
    $studentId = auth()->id();
    $classroomIds = ClassroomStudent::where('student_id', $studentId)->pluck('classroom_id')->toArray();

    $exams = ExamReport::whereIn('classroom_id', $classroomIds)
        ->with('classroom')
        ->orderByDesc('exam_date')
        ->paginate(12);

    return view('student.exams.index', compact('exams'));
}

// Student: show an exam (info + link to result)
public function studentShow(ExamReport $exam)
{
    $studentId = auth()->id();
    $enrolled = ClassroomStudent::where('classroom_id', $exam->classroom_id)
        ->where('student_id', $studentId)->exists();

    abort_unless($enrolled, 403);

    // check if result rows exist for this student
    $results = ExamResult::with('subject')
        ->where('exam_report_id', $exam->id)
        ->where('student_id', $studentId)
        ->get();

    $totalObtained = $results->sum('marks_obtained');
    $totalMarks    = $results->sum('total_marks');

    return view('student.exams.show', compact('exam','results','totalObtained','totalMarks'));
}

}
