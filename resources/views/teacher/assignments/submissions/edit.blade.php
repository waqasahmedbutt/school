@extends('layouts.app')
@section('title','Grade Submission')

@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-3">Grade — {{ $submission->student->name ?? 'Student' }}</h1>
  <div class="text-sm text-gray-500 mb-4">Assignment: {{ $assignment->title }}</div>

  <div class="bg-white p-4 rounded shadow mb-4">
    <div class="mb-3"><strong>Submitted at:</strong> {{ $submission->submitted_at ? $submission->submitted_at->format('Y-m-d H:i') : '—' }}</div>

    <div class="mb-3">
      <strong>Answer (text):</strong>
      <div class="mt-2 p-3 bg-gray-50 rounded text-sm">{{ $submission->answer_text ?? '—' }}</div>
    </div>

    <div class="mb-3">
      <strong>Files:</strong>
      <div class="mt-2">
        @if($submission->files && is_array($submission->files))
          @foreach($submission->files as $file)
            <div><a href="{{ asset('storage/'.$file) }}" target="_blank" class="text-blue-600 underline">{{ basename($file) }}</a></div>
          @endforeach
        @else
          <div class="text-gray-500">No files</div>
        @endif
      </div>
    </div>
  </div>

  <form action="{{ route('assignments.submissions.update', $submission) }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white p-4 rounded shadow space-y-4">
      <div>
        <label class="block text-sm">Marks awarded (out of {{ $assignment->total_marks ?? 0 }})</label>
        <input type="number" name="marks_awarded" min="0" max="{{ $assignment->total_marks ?? 0 }}" value="{{ old('marks_awarded', $submission->marks_awarded) }}" class="border px-3 py-2 rounded w-48">
      </div>

      <div>
        <label class="block text-sm">Feedback (optional)</label>
        <textarea name="feedback" rows="4" class="border px-3 py-2 rounded w-full">{{ old('feedback', $submission->feedback) }}</textarea>
      </div>

      <div class="flex justify-end">
        <a href="{{ route('assignments.submissions.index', $assignment) }}" class="mr-2 px-3 py-2 bg-gray-200 rounded">Cancel</a>
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
      </div>
    </div>
  </form>
</div>
@endsection
