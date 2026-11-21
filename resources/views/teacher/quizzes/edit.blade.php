@extends('layouts.app')
@section('title','Edit Quiz')

@section('content')
<div class="max-w-5xl mx-auto">
  <h1 class="text-2xl font-bold mb-6">Edit Quiz — {{ $quiz->title }}</h1>

  <form action="{{ route('teacher.quizzes.update', $quiz) }}" method="POST">
    @csrf @method('PUT')

    <div class="bg-white p-6 rounded shadow space-y-6">

      {{-- Section --}}
  <div>
  <label class="block text-sm font-medium mb-1">Section & Subject</label>
  <select name="section_id" required class="border px-3 py-2 rounded w-full mb-2">
    <option value="">-- select section --</option>
    @foreach($sections as $s)
      <option value="{{ $s->section->id }}" 
        @if(old('section_id', $quiz->section_id ?? null) == $s->section->id) selected @endif>
        {{ $s->section->name }}
      </option>
    @endforeach
  </select>

  <select name="subject_id" required class="border px-3 py-2 rounded w-full">
    <option value="">-- select subject --</option>
    @foreach($sections as $s)
      <option value="{{ $s->subject->id ?? '' }}"
        @if(old('subject_id', $quiz->subject_id ?? null) == ($s->subject->id ?? null)) selected @endif>
        {{ $s->subject->name ?? '' }}
      </option>
    @endforeach
  </select>
</div>

      {{-- Title --}}
      <div>
        <label class="block text-sm font-medium mb-1">Title</label>
        <input type="text" name="title" value="{{ old('title', $quiz->title) }}" 
               required class="border px-3 py-2 rounded w-full">
      </div>

      {{-- Description --}}
      <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="3"
                  class="border px-3 py-2 rounded w-full">{{ old('description', $quiz->description) }}</textarea>
      </div>

      {{-- Duration --}}
      <div>
        <label class="block text-sm font-medium mb-1">Duration (minutes)</label>
        <input type="number" name="duration_minutes" 
               value="{{ old('duration_minutes', $quiz->duration_minutes) }}" 
               class="border px-3 py-2 rounded w-48">
      </div>

      {{-- Questions --}}
      <div>
        <h2 class="text-lg font-semibold mt-6 mb-2">Questions</h2>

        <div class="space-y-6">
          @foreach($quiz->questions as $qIndex => $question)
            <div class="border p-4 rounded bg-gray-50">
              <label class="block text-sm font-medium mb-1">Question {{ $qIndex+1 }}</label>
              <input type="text" 
                     name="questions[{{ $question->id }}][text]" 
                     value="{{ old("questions.$question->id.text", $question->question) }}"
                     class="border px-3 py-2 rounded w-full mb-3">

              <label class="block text-sm font-medium mb-2">Options</label>
              @foreach($question->options as $oIndex => $option)
                <div class="flex items-center mb-2">
                  <input type="text"
                         name="questions[{{ $question->id }}][options][{{ $option->id }}][text]"
                         value="{{ old("questions.$question->id.options.$option->id.text", $option->option_text) }}"
                         class="border px-3 py-2 rounded flex-1 mr-2">

                  <label class="flex items-center">
                    <input type="radio" 
                           name="questions[{{ $question->id }}][correct_option_id]"
                           value="{{ $option->id }}"
                           {{ $option->is_correct ? 'checked' : '' }}>
                    <span class="ml-1 text-sm">Correct</span>
                  </label>
                </div>
              @endforeach
            </div>
          @endforeach
        </div>
      </div>

      {{-- Submit --}}
      <div class="flex justify-end pt-4">
        <a href="{{ route('teacher.classrooms.show', $quiz->classroom) }}" 
           class="mr-2 px-3 py-2 bg-gray-200 rounded">Cancel</a>
        <button class="px-4 py-2 bg-yellow-500 text-white rounded">Update Quiz</button>
      </div>
    </div>
  </form>
</div>
@endsection
