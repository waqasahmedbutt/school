@extends('layouts.app')
@section('title', 'Submissions — ' . $assignment->title)

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-bold">Submissions — {{ $assignment->title }}</h1>
      <div class="text-sm text-gray-500">Class: {{ $assignment->classroom->name }} • Section: {{ $assignment->section->name ?? '—' }}</div>
    </div>
    <a href="{{ route('teacher.assignments.edit', $assignment) }}" class="px-3 py-2 bg-gray-100 rounded">Back to Assignment</a>
  </div>

  <div class="bg-white p-4 rounded shadow">
    @if($submissions->isEmpty())
      <p class="text-gray-500">No submissions yet.</p>
    @else
      <table class="w-full table-auto">
        <thead>
          <tr class="text-left">
            <th class="p-2">Student</th>
            <th class="p-2">Submitted At</th>
            <th class="p-2">Files</th>
            <th class="p-2">Marks</th>
            <th class="p-2">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($submissions as $s)
            <tr class="border-t">
              <td class="p-2">{{ $s->student->name ?? 'ID '.$s->student_id }}</td>
              <td class="p-2">{{ $s->submitted_at ? $s->submitted_at->format('Y-m-d H:i') : '—' }}</td>
              <td class="p-2">
                @if($s->files && is_array($s->files))
                  @foreach($s->files as $file)
                    <div><a href="{{ asset('storage/'.$file) }}" target="_blank" class="text-blue-600 underline text-sm">{{ basename($file) }}</a></div>
                  @endforeach
                @else
                  <span class="text-gray-500">—</span>
                @endif
              </td>
              <td class="p-2">
                <div class="font-medium">{{ $s->marks_awarded ?? 'Not graded' }}</div>
              </td>
              <td class="p-2">
                <a href="{{ route('assignments.submissions.edit', $s) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Grade</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-4">
        {{ $submissions->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
