@extends('layouts.app')
@section('title','Enter Marks')

@section('content')
<div class="max-w-5xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Enter Marks — {{ $exam->title }}</h1>

  <form action="{{ route('teacher.exams.save-marks', [$exam->id, $subjectId]) }}" method="POST">
    @csrf

    <input type="hidden" name="section_id" value="{{ $sectionId }}">

    <div class="bg-white shadow rounded p-4 mb-4">
      <div class="flex items-center justify-between mb-3">
        <div>
          <h2 class="font-semibold">
  Subject: {{ $subject->name ?? 'Subject ID '.$subjectId }}
</h2>
          <div class="text-xs text-gray-500">Classroom: {{ $exam->classroom->name ?? '—' }} • Section: {{ $sectionId }}</div>
        </div>

        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Full Marks</label>
          <input type="number" name="total_marks" value="{{ old('total_marks', $results->first()->total_marks ?? 100) }}" class="border px-3 py-1 rounded w-24">
        </div>
      </div>

      <table class="w-full text-left">
        <thead>
          <tr class="border-b">
            <th class="p-2">Student</th>
            <th class="p-2">Marks Obtained</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $cs)
            @php
              $res = $results->firstWhere('student_id', $cs->student_id);
              $value = $res->marks_obtained ?? '';
            @endphp
            <tr class="border-b">
              <td class="p-2">{{ $cs->student->name }}</td>
              <td class="p-2">
                <input type="number" name="marks[{{ $cs->student_id }}]" value="{{ old('marks.'.$cs->student_id, $value) }}" class="border p-1 rounded w-24" min="0">
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="2" class="p-3 text-gray-500">No students in this section.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="flex justify-end">
      <a href="{{ url()->previous() }}" class="mr-2 px-4 py-2 bg-gray-200 rounded">Cancel</a>
      <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Save Marks</button>
    </div>
  </form>
</div>
@endsection
