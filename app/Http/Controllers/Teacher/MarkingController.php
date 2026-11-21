<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\TeacherAssignment;
use Illuminate\Support\Facades\Auth;

class MarkingController extends Controller
{
    protected function teacherId()
    {
        return Auth::id();
    }

    protected function isAssignedToClassroom($classroomId)
    {
        return TeacherAssignment::where('teacher_id', $this->teacherId())
            ->where('classroom_id', $classroomId)
            ->exists();
    }

    /* ---------- ASSIGNMENT SUBMISSIONS ---------- */
    public function assignmentSubmissionsIndex(Assignment $assignment)
    {
        // teacher must be creator or assigned to that classroom
        if ($assignment->created_by != $this->teacherId() && !$this->isAssignedToClassroom($assignment->classroom_id)) {
            abort(403);
        }

        // load submissions with student
        $submissions = $assignment->submissions()->with('student')->orderByDesc('submitted_at')->paginate(25);

        return view('teacher.assignments.submissions.index', compact('assignment','submissions'));
    }

    public function editSubmission(AssignmentSubmission $submission)
    {
        $assignment = $submission->assignment;
        if ($assignment->created_by != $this->teacherId() && !$this->isAssignedToClassroom($assignment->classroom_id)) {
            abort(403);
        }

        return view('teacher.assignments.submissions.edit', compact('submission','assignment'));
    }

    public function updateSubmission(Request $request, AssignmentSubmission $submission)
    {
        $assignment = $submission->assignment;
        if ($assignment->created_by != $this->teacherId() && !$this->isAssignedToClassroom($assignment->classroom_id)) {
            abort(403);
        }

        $max = $assignment->total_marks ?? 0;

        $data = $request->validate([
            'marks_awarded' => ['nullable','numeric','min:0', 'max:'.$max],
            'feedback' => ['nullable','string'],
        ]);

        $submission->update([
            'marks_awarded' => $data['marks_awarded'],
            'feedback' => $data['feedback'] ?? null,
        ]);

        return redirect()->route('assignments.submissions.index', $assignment)
            ->with('success','Submission updated.');
    }

    /* ---------- QUIZ ATTEMPTS ---------- */
    public function quizAttemptsIndex(Quiz $quiz)
    {
        // teacher must be creator or assigned to that classroom
        if ($quiz->created_by != $this->teacherId() && !$this->isAssignedToClassroom($quiz->classroom_id)) {
            abort(403);
        }

        $attempts = QuizAttempt::where('quiz_id', $quiz->id)->with('student')->orderByDesc('finished_at')->paginate(25);

        return view('teacher.quizzes.attempts.index', compact('quiz','attempts'));
    }

    public function quizAttemptShow(QuizAttempt $attempt)
    {
        $quiz = $attempt->quiz;
        if ($quiz->created_by != $this->teacherId() && !$this->isAssignedToClassroom($quiz->classroom_id)) {
            abort(403);
        }

        // load quiz Qs + options for context
        $quiz->load('questions.options');

        // answers stored in $attempt->answers (array) — show them alongside correct options
        $attempt->load('student');

        // compute total possible marks (sum of question marks)
        $totalMarks = $quiz->questions->sum('marks');

        return view('teacher.quizzes.attempts.show', compact('quiz','attempt','totalMarks'));
    }

    public function quizAttemptUpdate(Request $request, QuizAttempt $attempt)
    {
        $quiz = $attempt->quiz;
        if ($quiz->created_by != $this->teacherId() && !$this->isAssignedToClassroom($quiz->classroom_id)) {
            abort(403);
        }

        $totalMarks = $quiz->questions->sum('marks');

        $data = $request->validate([
            'score' => 'required|numeric|min:0|max:'.$totalMarks,
        ]);

        $attempt->update([
            'score' => $data['score']
        ]);

        return redirect()->route('quizzes.attempts.show', $attempt)->with('success','Attempt score updated.');
    }
}
