@extends('layouts.app')

@section('title', ($classroom->name ?? 'Classroom') . ' — Section ' . ($section->name ?? ''))

@section('content')
<div class="max-w-6xl mx-auto space-y-4 px-4 py-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">
        {{ $classroom->name ?? 'Classroom' }} — Section {{ $section->name ?? '—' }}
      </h1>
      <p class="text-sm text-gray-500 mt-1">
        Classroom description: {{ $classroom->description ?? '—' }}
      </p>
    </div>

    <div>
      <a href="{{ route('admin.classrooms.show', $classroom) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">
        <i class="fas fa-arrow-left"></i> Back to Classroom
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="p-3 bg-green-50 text-green-800 rounded">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="p-3 bg-red-50 text-red-800 rounded">{{ session('error') }}</div>
  @endif

  <div class="grid md:grid-cols-2 gap-4">
    {{-- Students card --}}
    <div class="bg-white p-4 rounded shadow">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold">Students</h2>
        <span class="text-xs text-gray-500">Manage students in this section</span>
      </div>

      {{-- Add student form --}}
      <form action="{{ route('admin.classrooms.sections.students.add', [$classroom->id, $section->id]) }}" method="POST" class="space-y-3 mb-4">
        @csrf

        <div class="flex flex-col sm:flex-row gap-2">
          <label for="student_id" class="sr-only">Select student</label>
          <select id="student_id" name="student_id" required class="border rounded px-3 py-2 w-full">
            <option value="">— Select student (unassigned students) —</option>
            @foreach($availableStudents ?? collect() as $student)
              <option value="{{ $student->id }}">{{ $student->name }} — {{ $student->email }}</option>
            @endforeach
          </select>

          <label for="roll_no" class="sr-only">Roll no</label>
          <input id="roll_no" type="text" name="roll_no" placeholder="Roll no" class="border rounded px-3 py-2 w-36" required/>

          <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">Add</button>
        </div>

        @error('student_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        @error('roll_no') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </form>

      <div class="space-y-2">
        @if(!empty($studentsPivot) && $studentsPivot->count())
          @foreach($studentsPivot as $pivot)
            <div class="flex items-center justify-between border rounded p-2">
              <div>
                <div class="font-medium">{{ $pivot->student->name ?? '—' }}</div>
                <div class="text-xs text-gray-500">{{ $pivot->student->email ?? '—' }} • Roll: {{ $pivot->roll_no ?? '—' }}</div>
              </div>

              <form action="{{ route('admin.classrooms.sections.students.remove', [$classroom->id, $section->id, $pivot->id]) }}" method="POST" onsubmit="return confirm('Remove student from this section?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">Remove</button>
              </form>
            </div>
          @endforeach
        @else
          <p class="text-gray-500">No students in this section yet.</p>
        @endif
      </div>
    </div>

    {{-- Teachers & Subjects card --}}
    <div class="bg-white p-4 rounded shadow">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold">Teachers & Subjects</h2>
        <span class="text-xs text-gray-500">Assign teachers to one or more subjects</span>
      </div>

      {{-- Assign teacher form --}}
      <form action="{{ route('admin.classrooms.sections.teachers.add', [$classroom->id, $section->id]) }}" method="POST" class="space-y-3 mb-4">
        @csrf

        <div class="grid grid-cols-1 gap-2">
          <div>
            <label for="teacher_id" class="block text-xs font-medium text-gray-700">Teacher</label>
            <select id="teacher_id" name="teacher_id" required class="border rounded px-3 py-2 w-full">
              <option value="">— Select teacher —</option>
              @foreach($teachers ?? collect() as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
              @endforeach
            </select>
            @error('teacher_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="subject_ids" class="block text-xs font-medium text-gray-700">Subjects (hold Ctrl / Cmd to multi-select)</label>
            <select id="subject_ids" name="subject_ids[]" required multiple class="border rounded px-3 py-2 w-full h-36">
              @foreach($subjects ?? collect() as $sub)
                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
              @endforeach
            </select>
            @error('subject_ids') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
          </div>

          <div class="flex justify-end">
            <button type="submit" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">Assign Teacher</button>
          </div>
        </div>
      </form>

      {{-- Existing teacher assignments grouped by teacher --}}
      <div class="space-y-3">
        @php
          // allow $teacherAssignments to be either grouped collection or plain array
          $groups = $teacherAssignments ?? collect();
        @endphp

        @if($groups->isEmpty())
          <p class="text-gray-500">No teachers assigned to this section yet.</p>
        @else
          @foreach($groups as $teacherId => $assignments)
            @php
              // if grouped by teacher id the inner is a collection of TeacherAssignment models
              $first = is_array($assignments) ? (count($assignments) ? $assignments[0] : null) : $assignments->first();
              $teacher = $first->teacher ?? null;
            @endphp

            <div class="border rounded p-3">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <div class="font-medium">{{ $teacher->name ?? 'Unknown teacher' }}</div>
                  <div class="text-xs text-gray-600 mt-1">
                    Subjects:
                    @foreach($assignments as $a)
                      <span class="inline-block px-2 py-0.5 bg-gray-100 rounded text-xs mr-1">{{ optional($a->subject)->name ?? '—' }}</span>
                    @endforeach
                  </div>
                </div>

                <div class="flex flex-col gap-2">
                  @foreach($assignments as $a)
                    <form method="POST" action="{{ route('admin.classrooms.sections.teachers.remove', [$classroom->id, $section->id, $a->id]) }}" onsubmit="return confirm('Remove this subject assignment?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">
                        Remove {{ optional($a->subject)->name ?? 'subject' }}
                      </button>
                    </form>
                  @endforeach
                </div>
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
