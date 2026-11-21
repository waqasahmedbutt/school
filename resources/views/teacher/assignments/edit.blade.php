@extends('layouts.app')
@section('title','Edit Assignment')

@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Edit Assignment</h1>

  <form action="{{ route('teacher.assignments.update', $assignment) }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white p-6 rounded shadow space-y-4">
      <div>
  <label class="block text-sm">Section</label>
  <select name="section_id" required class="border px-3 py-2 rounded w-full">
    @foreach($sections as $s)
      <option value="{{ $s->section->id }}" {{ $assignment->section_id == $s->section->id ? 'selected' : '' }}>
        {{ $s->section->name }}
      </option>
    @endforeach
  </select>
</div>

<div>
  <label class="block text-sm">Subject</label>
  <select name="subject_id" required class="border px-3 py-2 rounded w-full">
    @foreach($sections as $s)
      <option value="{{ $s->subject->id }}" {{ $assignment->subject_id == $s->subject->id ? 'selected' : '' }}>
        {{ $s->subject->name }}
      </option>
    @endforeach
  </select>
</div>

      <div>
        <label class="block text-sm">Title</label>
        <input name="title" value="{{ old('title', $assignment->title) }}" required class="border px-3 py-2 rounded w-full" />
      </div>

      <div>
        <label class="block text-sm">Description</label>
        <textarea name="description" class="border px-3 py-2 rounded w-full">{{ old('description', $assignment->description) }}</textarea>
      </div>

      <div class="flex gap-2">
        <div>
          <label class="block text-sm">Total marks</label>
          <input name="total_marks" value="{{ old('total_marks', $assignment->total_marks) }}" class="border px-3 py-2 rounded" />
        </div>
        <div>
          <label class="block text-sm">Due at</label>
          <input name="due_at" type="datetime-local" value="{{ optional($assignment->due_at)->format('Y-m-d\TH:i') }}" class="border px-3 py-2 rounded" />
        </div>
      </div>

      <div class="flex justify-end">
        <a href="{{ route('teacher.classrooms.show', $assignment->classroom) }}" class="mr-2 px-3 py-2 bg-gray-200 rounded">Cancel</a>
        <button class="px-4 py-2 bg-yellow-500 text-white rounded">Update</button>
      </div>
    </div>
  </form>
</div>
@endsection
