<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\School;
use App\Models\TimetableEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class SubjectController extends Controller
{
     public function Dashboard(Request $request)
    {
        // basic protection: only admins
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403);
        }

        // counts
        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalParents  = User::where('role', 'parent')->count();

        $totalClassrooms = Classroom::count();
        $totalSubjects   = Subject::count();
        $totalSchools    = School::count();

        // Timetable model may not exist in your app; check first
       $totalTimetables = 0;
       try {
    // Count distinct timetables if timetable_id exists, otherwise count all rows
    $totalTimetables = DB::table('timetable_entries')
        ->select(DB::raw('COUNT(DISTINCT timetable_id) as count'))
        ->value('count');
} catch (\Throwable $e) {
    // log and fallback
    \Log::warning('Failed to count timetable_entries: '.$e->getMessage());
    $totalTimetables = 0;
}
        // recent lists for quick actions
        $recentUsers = User::orderByDesc('created_at')->limit(6)->get();
        $recentClassrooms = Classroom::with('school')->orderByDesc('created_at')->limit(6)->get();

        // optional KPI: active today (attendance)
        $activeToday = null;
        if (class_exists(\App\Models\Attendance::class)) {
            $activeToday = \App\Models\Attendance::whereDate('date', now()->toDateString())->distinct('student_id')->count('student_id');
        }

        return view('admin.dashboard.index', compact(
            'totalUsers','totalStudents','totalTeachers','totalParents',
            'totalClassrooms','totalSubjects','totalTimetables','totalSchools',
            'recentUsers','recentClassrooms','activeToday'
        ));
    }
    public function index()
    {
        $subjects = Subject::orderBy('name')->paginate(20);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'code' => ['nullable','string','max:50'],
            'description' => ['nullable','string'],
        ]);

        Subject::create($data);
        return redirect()->route('admin.subjects.index')->with('success','Subject created.');
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'code' => ['nullable','string','max:50'],
            'description' => ['nullable','string'],
        ]);

        $subject->update($data);
        return redirect()->route('admin.subjects.index')->with('success','Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return back()->with('success','Subject deleted.');
    }
}
