<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Section;
use App\Models\School;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $query = Classroom::with('sections', 'school');

        // filter by school if provided
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // simple search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $classrooms = $query->orderBy('name')->paginate(12)->appends($request->query());

        // load schools for the filter dropdown
        $schools = School::orderBy('name')->get();

        return view('admin.classrooms.index', compact('classrooms', 'schools'));
    }

    public function create()
    {
        // pass schools for dropdown
        $schools = School::orderBy('name')->get();
        return view('admin.classrooms.create', compact('schools'));
    }
 public function store1(Request $request){
    $data =$request->validate([
        'schoolid' => 'required'
    ]);
 }
    public function store(Request $request)
    {
        $data = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:191|unique:classrooms,name',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0',
            'sections' => 'nullable|array',
            'sections.*.name' => 'required_with:sections|string|max:150',
            'sections.*.code' => 'nullable|string|max:50',
        ]);

        $classroom = Classroom::create([
            'school_id'   => $data['school_id'],
            'name'        => $data['name'],
            'slug'        => \Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'capacity'    => $data['capacity'] ?? null,
            'created_by'  => auth()->id(),
        ]);

        if (!empty($data['sections'])) {
            foreach ($data['sections'] as $s) {
                Section::create([
                    'classroom_id' => $classroom->id,
                    'name'         => $s['name'],
                    'code'         => $s['code'] ?? null,
                    'capacity'     => null,
                ]);
            }
        }

        return redirect()->route('admin.classrooms.index')->with('success', 'Classroom created successfully.');
    }

    public function show(Classroom $classroom)
    {
        $classroom->load('sections', 'school');
        return view('admin.classrooms.show', compact('classroom'));
    }

    public function edit(Classroom $classroom)
    {
        $classroom->load('sections', 'school');
        $schools = School::orderBy('name')->get();
        return view('admin.classrooms.edit', compact('classroom', 'schools'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'name' => ['required','string','max:191', Rule::unique('classrooms','name')->ignore($classroom->id)],
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0',
            'sections' => 'nullable|array',
            'sections.*.id' => 'nullable|exists:sections,id',
            'sections.*.name' => 'required_with:sections|string|max:150',
            'sections.*.code' => 'nullable|string|max:50',
        ]);

        $classroom->update([
            'school_id'   => $data['school_id'],
            'name'        => $data['name'],
            'slug'        => \Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'capacity'    => $data['capacity'] ?? null,
        ]);

        // update/create sections
        if (!empty($data['sections'])) {
            foreach ($data['sections'] as $s) {
                if (!empty($s['id'])) {
                    $section = Section::find($s['id']);
                    if ($section && $section->classroom_id == $classroom->id) {
                        $section->update([
                            'name' => $s['name'],
                            'code' => $s['code'] ?? null,
                        ]);
                    }
                } else {
                    Section::create([
                        'classroom_id' => $classroom->id,
                        'name'         => $s['name'],
                        'code'         => $s['code'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.classrooms.index')->with('success', 'Classroom updated.');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->sections()->delete();
        $classroom->delete();

        return redirect()->route('admin.classrooms.index')->with('success', 'Classroom deleted.');
    }
}
