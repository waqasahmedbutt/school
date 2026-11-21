@extends('layouts.app')
@section('title','Attendance Records')

@section('content')
<div class="max-w-5xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Attendance Records — {{ $classroom->name }} / Section {{ $section->name }}</h1>

  {{-- Subject Filter --}}
  <form method="GET" class="mb-4 flex gap-2 items-center">
    <label>Subject:</label>
    <select name="subject_id" onchange="this.form.submit()" class="border px-3 py-2 rounded">
      @foreach($subjects as $sub)
        <option value="{{ $sub->subject_id }}" {{ $sub->subject_id == $subjectId ? 'selected' : '' }}>
          {{ $sub->subject->name }}
        </option>
      @endforeach
    </select>
    <a href="{{ route('teacher.attendance.records', [$classroom->id,$section->id]) }}" class="ml-2 text-sm text-gray-600 underline">Clear</a>
  </form>

  {{-- Add New Attendance --}}
  <div class="mb-4">
    <a href="{{ route('teacher.attendance.form', [$classroom->id,$section->id]) }}?subject_id={{ $subjectId }}" class="px-3 py-2 bg-green-600 text-white rounded">+ Add New Attendance</a>
  </div>

  {{-- Records Table --}}
  <div class="bg-white p-4 rounded shadow">
    @if($summary->isEmpty())
      <div class="text-gray-500 p-4">No attendance records found.</div>
    @else
      <table class="w-full table-auto border">
        <thead>
          <tr class="text-left">
            <th class="p-2 border">Date</th>
            <th class="p-2 border">Total</th>
            <th class="p-2 border text-green-600">Present</th>
            <th class="p-2 border text-red-600">Absent</th>
            <th class="p-2 border text-yellow-600">Late</th>
            <th class="p-2 border text-blue-600">Excused</th>
            <th class="p-2 border">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($summary as $rec)
            <tr class="border-t">
              <td class="p-2 border">{{ $rec->date }}</td>
              <td class="p-2 border">{{ $rec->total_students }}</td>
              <td class="p-2 border">{{ $rec->presents }}</td>
              <td class="p-2 border">{{ $rec->absents }}</td>
              <td class="p-2 border">{{ $rec->lates }}</td>
              <td class="p-2 border">{{ $rec->excused }}</td>
              <td class="p-2 border">
                <a href="{{ route('teacher.attendance.details', [$classroom->id,$section->id,$rec->date]) }}?subject_id={{ $subjectId }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">View</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="mt-4">{{ $summary->links() }}</div>
    @endif
  </div>
</div>
@endsection
