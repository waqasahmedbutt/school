@extends('layouts.app')
@section('title','Attendance Details')

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-xl font-bold mb-4">
    Attendance on {{ $date }} — {{ $classroom->name }} / {{ $section->name }}
  </h1>

  <form method="POST" action="{{ route('teacher.attendance.update', [$classroom->id, $section->id, $date]) }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="subject_id" value="{{ $subjectId }}">

    <table class="w-full table-auto border">
      <thead>
        <tr>
          <th class="p-2 border">Roll / Name</th>
          <th class="p-2 border">Status</th>
          <th class="p-2 border">Notes</th>
        </tr>
      </thead>
      <tbody>
        @foreach($records as $rec)
          <tr>
            <td class="p-2 border">{{ $rec->student->name }}</td>
            <td class="p-2 border">
              @foreach(['present','absent','late','excused'] as $s)
                <label class="inline-flex items-center mr-2">
                  <input type="radio" name="attendance[{{ $rec->student_id }}][status]" value="{{ $s }}" {{ $rec->status === $s ? 'checked' : '' }}>
                  {{ ucfirst($s) }}
                </label>
              @endforeach
            </td>
            <td class="p-2 border">
              <input type="text" name="attendance[{{ $rec->student_id }}][notes]" value="{{ $rec->notes }}" class="border px-2 py-1 rounded w-full">
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="mt-4 flex justify-end gap-2">
      <a href="{{ route('teacher.attendance.records', [$classroom->id,$section->id]) }}?subject_id={{ $subjectId }}" class="px-3 py-2 bg-gray-200 rounded">Back</a>
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Update Attendance</button>
    </div>
  </form>
</div>
@endsection
