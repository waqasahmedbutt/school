@extends('layouts.app')
@section('title',$assignment->title)

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">{{ $assignment->title }}</h1>

  <div class="bg-white p-4 rounded shadow mb-4">
    <div class="text-sm text-gray-500 mb-2">Class: {{ $assignment->classroom->name }} • Section: {{ $assignment->section->name ?? '—' }}</div>
    <div class="mb-3">{{ $assignment->description }}</div>
    <div class="text-xs text-gray-500">Total Marks: {{ $assignment->total_marks ?? '—' }}</div>
    <div class="text-xs text-gray-500">Due: {{ $assignment->due_at? $assignment->due_at->format('Y-m-d H:i') : '—' }}</div>
  </div>

  <div class="bg-white p-4 rounded shadow">
    <h3 class="font-semibold mb-3">Submit Assignment</h3>
    @if($submission)
      <div class="mb-3 text-sm text-green-700">You submitted on {{ $submission->submitted_at->format('Y-m-d H:i') }}</div>
      <div class="mb-3 text-sm">Marks: {{ $submission->marks_awarded ?? 'Not graded' }}</div>
      <div class="mb-3 text-sm">Feedback: {{ $submission->feedback ?? '—' }}</div>
    @endif

    <form action="{{ route('student.assignments.submit', $assignment) }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="mb-3">
        <label class="block text-sm">Answer (text)</label>
        <textarea name="answer_text" rows="4" class="border px-3 py-2 rounded w-full">{{ old('answer_text', $submission->answer_text ?? '') }}</textarea>
      </div>

      <div class="mb-3">
        <label class="block text-sm">Files (optional)</label>
        <input type="file" name="files[]" multiple>
      </div>

      <div class="text-right">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Submit</button>
      </div>
    </form>
  </div>
</div>
@endsection
