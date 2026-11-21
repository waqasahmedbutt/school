@extends('layouts.app')
@section('title','Mark Attendance')

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">
    Mark Attendance — {{ $classroom->name }} / Section {{ $section->name }}
  </h1>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('teacher.attendance.store', [$classroom->id, $section->id]) }}">
    @csrf

    <div class="mb-4 flex gap-4 items-center">
      <label>Date:</label>
      <input type="date" name="date" value="{{ old('date',$date) }}" class="border px-3 py-2 rounded">
    </div>

    {{-- Subject --}}
    <div class="mb-4">
      @if($myAssignments->count() > 1)
        <label>Subject:</label>
        <select name="subject_id" class="border px-3 py-2 rounded">
          @foreach($myAssignments as $ma)
            <option value="{{ $ma->subject_id }}" {{ $ma->subject_id == $subjectId ? 'selected' : '' }}>
              {{ $ma->subject->name }}
            </option>
          @endforeach
        </select>
      @else
        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
        <div>Subject: {{ $myAssignments->first()->subject->name ?? '—' }}</div>
      @endif
    </div>

    {{-- Students Table --}}
    <div class="bg-white p-4 rounded shadow">
      <table class="w-full table-auto border">
        <thead>
          <tr>
            <th class="p-2 border">#</th>
            <th class="p-2 border">Roll / Name</th>
            <th class="p-2 border">Status</th>
            <th class="p-2 border">Notes</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $idx => $student)
            @php
              $existingRec = $existing->get($student->id);
              $status = old("attendance.$student->id.status", optional($existingRec)->status ?? 'absent');
              $notes = old("attendance.$student->id.notes", optional($existingRec)->notes ?? '');
            @endphp
            <tr>
              <td class="p-2 border">{{ $idx + 1 }}</td>
              <td class="p-2 border">{{ $student->name }}</td>
              <td class="p-2 border">
                @foreach(['present','absent','late','excused'] as $s)
                  <label class="inline-flex items-center mr-2">
                    <input type="radio" name="attendance[{{ $student->id }}][status]" value="{{ $s }}" {{ $status === $s ? 'checked' : '' }}>
                    {{ ucfirst($s) }}
                  </label>
                @endforeach
              </td>
              <td class="p-2 border">
                <input type="text" name="attendance[{{ $student->id }}][notes]" value="{{ $notes }}" class="border px-2 py-1 rounded w-full">
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="p-4 text-gray-500">No students in this section.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-end gap-2">
      <a href="{{ route('teacher.attendance.records', [$classroom->id, $section->id]) }}?subject_id={{ $subjectId }}" class="px-3 py-2 bg-gray-200 rounded">Back</a>
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Save Attendance</button>
    </div>
  </form>
</div>
@endsection
