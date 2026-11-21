@extends('layouts.app')
@section('title','Physical Exam: ' . $exam->title)

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold mb-3">{{ $exam->title }} — {{ optional($exam->exam_date)->format('Y-m-d') }}</h1>

  <form action="{{ route('teacher.physical_exams.marks.store', $exam) }}" method="POST">
    @csrf
    <div class="bg-white p-4 rounded shadow space-y-3">
      <div>
        <p class="text-sm text-gray-500">Subject: {{ $exam->subject->name ?? '–' }}</p>
        <p class="text-sm text-gray-500">Total Marks: {{ $exam->total_marks }}</p>
      </div>

      <div class="space-y-2">
        @foreach($students as $s)
          @php
            $studentModel = $s->student ?? null;
            $existing = $exam->marks->firstWhere('student_id', $studentModel->id ?? null);
          @endphp

          <div class="flex items-center justify-between border p-2 rounded">
            <div>
              <div class="font-medium">{{ $studentModel->name ?? 'Student' }}</div>
            </div>
            <div class="flex items-center gap-2">
              <input type="hidden" name="marks[{{ $loop->index }}][student_id]" value="{{ $studentModel->id ?? '' }}">
              <input type="number" step="0.01" name="marks[{{ $loop->index }}][marks_obtained]" value="{{ old('marks.'.$loop->index.'.marks_obtained', $existing->marks_obtained ?? '') }}" class="border px-3 py-1 rounded w-28" max="{{ $exam->total_marks }}" />
              <input type="text" name="marks[{{ $loop->index }}][remarks]" placeholder="Remarks" value="{{ old('marks.'.$loop->index.'.remarks', $existing->remarks ?? '') }}" class="border px-3 py-1 rounded" />
            </div>
          </div>
        @endforeach
      </div>

      <div class="flex justify-end">
        <a href="{{ route('teacher.classrooms.show', $exam->classroom) }}" class="mr-2 px-3 py-2 bg-gray-200 rounded">Back</a>
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Save Marks</button>
      </div>
    </div>
  </form>
</div>
@endsection
