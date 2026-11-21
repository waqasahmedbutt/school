<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\AssignedToClassroom;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Section;
use App\Models\ClassroomStudent;
use App\Models\User;
use App\Models\TeacherAssignment;
use App\Models\Subject;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function show(Classroom $classroom, Section $section)
    {
        // load students in this section via pivot model
        $studentsPivot = ClassroomStudent::with('student')
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->get();

        // teachers assigned to this classroom+section
        $teacherAssignments = TeacherAssignment::with('teacher','subject')
            ->where('classroom_id', $classroom->id)
            ->where('section_id', $section->id)
            ->get()
            ->groupBy('teacher_id');

        // available students to add: role=student AND NOT assigned to any classroom (per your rule)
        $availableStudents = User::where('role','student')
            ->whereDoesntHave('classrooms') // ensures student not present in any classroom-section
            ->orderBy('name')
            ->get();

        // teachers list (role=teacher)
        $teachers = User::where('role','teacher')->orderBy('name')->get();

        // subjects list (for teacher subject assignment)
        $subjects = Subject::orderBy('name')->get();

        return view('admin.sections.show', compact('classroom','section','studentsPivot','availableStudents','teachers','subjects','teacherAssignments'));
    }

    public function addStudent(Request $request, Classroom $classroom, Section $section)
    {
        $data = $request->validate([
            'student_id' => ['required','exists:users,id'],
        ]);

        $student = User::where('id', $data['student_id'])->where('role','student')->firstOrFail();

        // ensure student not assigned to any classroom already
        $already = ClassroomStudent::where('student_id',$student->id)->exists();
        if ($already) {
            return back()->with('error', 'Student is already assigned to a classroom/section.');
        }

        ClassroomStudent::create([
            'classroom_id' => $classroom->id,
            'section_id' => $section->id,
            'student_id' => $student->id,
            'roll_no' => $request->input('roll_no') ?? null,
        ]);

        return back()->with('success', 'Student added to section.');
    }

    public function removeStudent(Classroom $classroom, Section $section, ClassroomStudent $pivot)
    {
        // pivot is the classroom_student model row
        $pivot->delete();
        return back()->with('success', 'Student removed from section.');
    }

  public function addTeacher(Request $request, Classroom $classroom, Section $section)
{
    $data = $request->validate([
        'teacher_id' => ['required','exists:users,id'],
        'subject_ids' => 'required|array',
        'subject_ids.*' => 'exists:subjects,id',
    ]);

    $teacher = User::where('id',$data['teacher_id'])->where('role','teacher')->firstOrFail();

    foreach ($data['subject_ids'] as $subjectId) {
        $subject = Subject::findOrFail($subjectId); // ✅ fetch subject model

        // avoid duplicates
        $exists = TeacherAssignment::where([
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
            'section_id' => $section->id,
        ])->exists();

        if (!$exists) {
            TeacherAssignment::create([
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id,
                'classroom_id' => $classroom->id,
                'section_id' => $section->id,
            ]);

            // ✅ pass subject model (with name)
            // $teacher->notify(new AssignedToClassroom($classroom, $section, $subject));
        }
    }

    return back()->with('success', 'Teacher assigned to section with selected subjects.');
}


    public function removeTeacher(Classroom $classroom, Section $section, TeacherAssignment $assignment)
    {
        // only allow removing assignments belonging to this classroom/section
        if ($assignment->classroom_id != $classroom->id || $assignment->section_id != $section->id) {
            abort(403);
        }
        $assignment->delete();

        return back()->with('success', 'Teacher assignment removed.');
    }

    public function destroy(Classroom $classroom, Section $section)
    {
        // remove pivot students and teacher assignments for this section
        ClassroomStudent::where('classroom_id',$classroom->id)->where('section_id',$section->id)->delete();
        TeacherAssignment::where('classroom_id',$classroom->id)->where('section_id',$section->id)->delete();

        $section->delete();

        return redirect()->route('admin.classrooms.show', $classroom)->with('success','Section deleted.');
    }
}
