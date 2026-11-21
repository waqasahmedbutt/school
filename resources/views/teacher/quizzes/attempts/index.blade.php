@extends('layouts.app')
@section('title','Quiz Attempts — ' . $quiz->title)

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-bold">Attempts — {{ $quiz->title }}</h1>
      <div class="text-sm text-gray-500">Class: {{ $quiz->classroom->name }} • Section: {{ $quiz->section->name ?? '—' }}</div>
    </div>
    <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="px-3 py-2 bg-gray-100 rounded">Back</a>
  </div>

  <div class="bg-white p-4 rounded shadow">
    @if($attempts->isEmpty())
      <p class="text-gray-500">No attempts yet.</p>
    @else
      <table class="w-full table-auto">
        <thead>
          <tr class="text-left">
            <th class="p-2">Student</th>
            <th class="p-2">Finished At</th>
            <th class="p-2">Score</th>
            <th class="p-2">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($attempts as $a)
            <tr class="border-t">
              <td class="p-2">{{ $a->student->name ?? 'ID '.$a->student_id }}</td>
              <td class="p-2">{{ $a->finished_at ? $a->finished_at->format('Y-m-d H:i') : 'In progress' }}</td>
              <td class="p-2">{{ $a->score ?? '—' }}</td>
              <td class="p-2">
                <a href="{{ route('quizzes.attempts.show', $a) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">View</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-4">
        {{ $attempts->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
