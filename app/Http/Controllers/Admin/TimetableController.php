<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TimetableEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimetableController extends Controller
{
    /**
     * Index - show sections that have timetable entries (one row per section).
     * Also supports filtering by classroom_id and section_id via querystring.
     */
    public function index(Request $request)
    {
        $classroomId = $request->query('classroom_id');
        $sectionId   = $request->query('section_id');

        // get classrooms for filters
        $classrooms = Classroom::with('sections')->orderBy('name')->get();

        // Query sections which have timetable entries
        $query = Section::with('classroom')
            ->whereHas('timetableEntries'); // ensures section has at least one entry

        if ($classroomId) {
            $query->where('classroom_id', $classroomId);
        }

        if ($sectionId) {
            $query->where('id', $sectionId);
        }

        $sections = $query->orderBy('classroom_id')->orderBy('name')->paginate(12);

        return view('admin.timetable.index', compact('sections', 'classrooms', 'classroomId', 'sectionId'));
    }

    /**
     * Show create form.
     * Accept optional ?classroom= & ?section= as query params to preselect.
     */
    public function create(Request $request)
    {
        $classrooms = Classroom::with('sections')->orderBy('name')->get();
        $subjects   = Subject::orderBy('name')->get();
        $teachers   = User::where('role', 'teacher')->orderBy('name')->get();

        // optional preselection
        $selectedClassroom = $request->query('classroom');
        $selectedSection   = $request->query('section');

        return view('admin.timetable.create', compact(
            'classrooms', 'subjects', 'teachers', 'selectedClassroom', 'selectedSection'
        ));
    }

    /**
     * Store multiple rows at once.
     * Each request->entries[] has: subject_id, teacher_id, days[] (array of ints 0..6), start_time, end_time, room
     */
    public function store(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'entries'    => 'required|array|min:1',
            'entries.*.subject_id' => 'required|exists:subjects,id',
            'entries.*.teacher_id' => 'required|exists:users,id',
            'entries.*.days'       => 'required|array|min:1',
            'entries.*.days.*'     => 'integer|min:0|max:6',
            'entries.*.start_time' => 'required|date_format:H:i',
            'entries.*.end_time'   => 'required|date_format:H:i|after:entries.*.start_time',
            'entries.*.room'       => 'nullable|string|max:100',
        ]);

        $sectionId = $request->input('section_id');

        DB::transaction(function () use ($request, $sectionId) {
            foreach ($request->input('entries', []) as $entry) {
                $subjectId = $entry['subject_id'];
                $teacherId = $entry['teacher_id'];
                $start     = $entry['start_time'];
                $end       = $entry['end_time'];
                $room      = $entry['room'] ?? null;
                $days      = $entry['days'];

                foreach ($days as $day) {
                    TimetableEntry::create([
                        'section_id' => $sectionId,
                        'subject_id' => $subjectId,
                        'teacher_id' => $teacherId,
                        'day_of_week'=> (int)$day,
                        'start_time' => $start,
                        'end_time'   => $end,
                        'room'       => $room,
                    ]);
                }
            }
        });

        return redirect()->route('admin.timetable.index', ['classroom_id' => null])
                         ->with('success', 'Timetable entries added successfully.');
    }

    /**
     * Show a single section's timetable (all periods), grouped by day and ordered by time.
     * Route signature: admin/timetable/{classroom}/{section}
     */
public function show($classroomId, $sectionId)
{
    // ensure classroom and section exist & match
    $classroom = Classroom::findOrFail($classroomId);
    $section = Section::where('id', $sectionId)->where('classroom_id', $classroomId)->firstOrFail();

    // load entries for this section, eager load subject & teacher
    $entries = TimetableEntry::with(['subject','teacher','section.classroom'])
        ->where('section_id', $section->id)
        ->orderBy('start_time')
        ->orderBy('day_of_week')
        ->get();

    // group by day_of_week
    $grouped = $entries->groupBy('day_of_week')->map(function ($g) {
        return $g->sortBy('start_time')->values();
    });

    // ensure every day 0..6 exists as a collection
    $days = [];
    for ($i = 0; $i <= 6; $i++) {
        $days[$i] = $grouped->has($i) ? $grouped->get($i) : collect();
    }

    // build unique time slots across all days, sorted by start time
    $slotsAssoc = [];
    foreach ($days as $dayIndex => $col) {
        foreach ($col as $entry) {
            $start = Carbon::parse($entry->start_time)->format('H:i');
            $end   = Carbon::parse($entry->end_time)->format('H:i');
            $key = $start . '|' . $end;
            $slotsAssoc[$key] = ['start' => $start, 'end' => $end];
        }
    }
    // sort slots by start time
    uasort($slotsAssoc, function($a,$b){
        return strtotime($a['start']) <=> strtotime($b['start']);
    });
    $slots = array_values($slotsAssoc);

    // Pass everything to the view (blade can use either $entries flat or $days/$slots grid)
    return view('admin.timetable.show', compact('classroom','section','entries','days','slots'));
}


    /**
     * Edit a single timetable entry
     * Route: admin/timetable/{timetable}/edit
     */
    public function edit(TimetableEntry $timetable)
    {
        $classrooms = Classroom::with('sections')->orderBy('name')->get();
        $subjects   = Subject::orderBy('name')->get();
        $teachers   = User::where('role', 'teacher')->orderBy('name')->get();

        return view('admin.timetable.edit', compact('timetable','classrooms','subjects','teachers'));
    }

    /**
     * Update a single timetable entry
     */
    public function update(Request $request, TimetableEntry $timetable)
    {
        $data = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week'=> 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'room'       => 'nullable|string|max:100',
        ]);

        $timetable->update($data);

        // Redirect back to the show page for that section
        $section = $timetable->section;
        return redirect()->route('admin.timetable.show', ['classroom' => $section->classroom_id, 'section' => $section->id])
            ->with('success', 'Timetable entry updated.');
    }

    /**
     * Delete a single timetable entry
     */
    public function destroy(TimetableEntry $timetable)
    {
        $section = $timetable->section;
        $timetable->delete();

        return redirect()->route('admin.timetable.show', ['classroom' => $section->classroom_id, 'section' => $section->id])
            ->with('success', 'Timetable entry removed.');
    }

    /**
     * Export current filter / section as PDF.
     * Accepts ?classroom_id= & ?section_id= (if you want the whole school timetable you can omit section)
     */
// at top

public function exportPdf(Request $request)
{
    // Accept either query params or explicit route values
    $classroomId = $request->query('classroom_id') ?? $request->route('classroom');
    $sectionId   = $request->query('section_id')   ?? $request->route('section');

    // If you want to allow exporting whole-school timetable, handle nulls
    $classroom = $classroomId ? Classroom::findOrFail($classroomId) : null;
    $section   = $sectionId ? Section::where('id', $sectionId)
                                ->when($classroomId, fn($q) => $q->where('classroom_id',$classroomId))
                                ->firstOrFail() : null;

    // Build query (if section provided, restrict to it; else optionally restrict to classroom)
    $query = TimetableEntry::with(['subject','teacher','section.classroom']);
    if ($section) {
        $query->where('section_id', $section->id);
    } elseif ($classroom) {
        // all entries in this classroom across sections
        $query->whereHas('section', fn($q) => $q->where('classroom_id', $classroom->id));
    }

    $entries = $query->orderBy('start_time')->orderBy('day_of_week')->get();

    // group by day_of_week
    $grouped = $entries->groupBy('day_of_week')->map(fn($g) => $g->sortBy('start_time')->values());

    // ensure every day 0..6 exists as a collection
    $days = [];
    for ($i = 0; $i <= 6; $i++) {
        $days[$i] = $grouped->has($i) ? $grouped->get($i) : collect();
    }

    // build unique time slots across all days, sorted by start time
    $slotsAssoc = [];
    foreach ($days as $col) {
        foreach ($col as $entry) {
            $start = Carbon::parse($entry->start_time)->format('H:i');
            $end   = Carbon::parse($entry->end_time)->format('H:i');
            $key = $start . '|' . $end;
            $slotsAssoc[$key] = ['start' => $start, 'end' => $end];
        }
    }
    uasort($slotsAssoc, fn($a,$b) => strtotime($a['start']) <=> strtotime($b['start']));
    $slots = array_values($slotsAssoc);

    // debug: if you want to quickly inspect the view before PDF generation, uncomment:
    // return view('admin.timetable.pdf', compact('classroom','section','entries','days','slots'));

    // Load view and create PDF
    $pdf = Pdf::loadView('admin.timetable.pdf', compact('classroom','section','entries','days','slots'));

    // Optional DomPDF options (landscape for wide tables, enable remote if you load external fonts/assets)
    $pdf->setPaper('a4', 'landscape');
    $pdf->getDomPDF()->set_option("isRemoteEnabled", true);

    $filename = 'timetable' . ($section ? "_classroom{$classroom?->id}_section{$section->id}" : ($classroom ? "_classroom{$classroom->id}" : '')) . '.pdf';
    return $pdf->download($filename);
}

}
