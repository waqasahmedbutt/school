@extends('layouts.app')
@section('title','Create Physical Exam')

@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Create Physical Exam — {{ $classroom->name }}</h1>

  <form action="{{ route('teacher.physical_exams.store', $classroom) }}" method="POST">
    @csrf
    <div class="bg-white p-6 rounded shadow space-y-4">
      <div>
        <label class="block text-sm">Section / Subject</label>
        <select name="assignment_id" required class="border px-3 py-2 rounded w-full">
          <option value="">-- select section & subject --</option>
          @foreach($assignments as $a)
            <option value="{{ $a->id }}">
              {{ $a->section->name ?? 'Section '.$a->section_id }} — {{ $a->subject->name ?? 'Subject' }}
            </option>
          @endforeach
        </select>
        @error('assignment_id') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="block text-sm">Title</label>
        <input name="title" value="{{ old('title') }}" required class="border px-3 py-2 rounded w-full" />
        @error('title') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="flex gap-2">
        <div>
          <label class="block text-sm">Exam date</label>
          <input name="exam_date" type="date" value="{{ old('exam_date') }}" required class="border px-3 py-2 rounded" />
          @error('exam_date') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="block text-sm">Total marks</label>
          <input name="total_marks" type="number" value="{{ old('total_marks', 100) }}" required class="border px-3 py-2 rounded" />
          @error('total_marks') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="flex justify-end">
        <a href="{{ route('teacher.classrooms.show', $classroom) }}" class="mr-2 px-3 py-2 bg-gray-200 rounded">Cancel</a>
        <button class="px-4 py-2 bg-green-600 text-white rounded">Create</button>
      </div>
    </div>
  </form>
</div>
@endsection
