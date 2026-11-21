@extends('layouts.app')
@section('title','Quiz Attempt')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
  
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Quiz Attempt — {{ $quiz->title }}</h1>
      <p class="text-sm text-gray-500">
        Student: <span class="font-medium">{{ $attempt->student->name ?? 'ID '.$attempt->student_id }}</span>
      </p>
    </div>
    <a href="{{ route('quizzes.attempts.index', $quiz) }}" 
       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium transition">
      ← Back to Attempts
    </a>
  </div>

  {{-- Success Message --}}
  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
      {{ session('success') }}
    </div>
  @endif

  {{-- Attempt Summary --}}
  <div class="bg-white p-6 rounded-xl shadow space-y-2">
    <p class="text-sm text-gray-500">
      Finished: 
      <span class="font-medium">
        {{ $attempt->finished_at ? $attempt->finished_at->format('Y-m-d H:i') : 'In progress' }}
      </span>
    </p>
    <div class="text-xl font-semibold">
      Score: 
      <span class="text-blue-600">{{ $attempt->score ?? '—' }}</span> / {{ $totalMarks }}
    </div>
  </div>

  {{-- Questions & Answers --}}
  <div class="bg-white p-6 rounded-xl shadow space-y-6">
    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2">Answers</h2>

    @php
      $answers = $attempt->answers ?? [];
      $answersByQ = collect($answers)->mapWithKeys(fn($a) => [$a['question_id'] => $a]);
    @endphp

    @foreach($quiz->questions as $qIdx => $q)
      <div class="border rounded-lg p-4">
        {{-- Question --}}
        <div class="flex justify-between items-center mb-3">
          <p class="font-medium text-gray-800">
            Q{{ $qIdx + 1 }}. {{ $q->question }}
          </p>
          <span class="text-sm text-gray-500">{{ $q->marks ?? 0 }} pts</span>
        </div>

        {{-- Options --}}
        <div class="space-y-2">
          @foreach($q->options as $opt)
            @php
              $selected = optional($answersByQ->get($q->id))['option_id'] ?? null;
              $isCorrect = (bool)$opt->is_correct;
            @endphp
            <div class="flex items-center justify-between p-2 rounded 
              {{ $isCorrect ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }}">
              <span class="{{ $isCorrect ? 'text-green-800 font-medium' : 'text-gray-700' }}">
                {{ $opt->option_text }}
              </span>
              <div class="space-x-2 text-xs">
                @if($selected == $opt->id)
                  <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">Selected</span>
                @endif
                @if($isCorrect)
                  <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Correct</span>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>

  {{-- Adjust Score --}}
  <form action="{{ route('quizzes.attempts.update', $attempt) }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white p-6 rounded-xl shadow space-y-4">
      <div>
        <label class="block text-sm text-gray-700 font-medium mb-1">
          Adjust Score (max {{ $totalMarks }})
        </label>
        <input type="number" name="score" min="0" max="{{ $totalMarks }}" 
               value="{{ old('score', $attempt->score) }}"
               class="border px-3 py-2 rounded w-40 focus:ring focus:ring-blue-300">
      </div>

      <div class="flex justify-end">
        <a href="{{ route('quizzes.attempts.index', $quiz) }}" 
           class="mr-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded transition">
          Cancel
        </a>
        <button class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition">
          Save Score
        </button>
      </div>
    </div>
  </form>
</div>
@endsection
