@extends('layouts.app')
@section('title','Create Assignment')

@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Create Assignment — {{ $classroom->name }}</h1>

  <form action="{{ route('teacher.assignments.store', $classroom) }}" method="POST">
    @csrf
    <div class="bg-white p-6 rounded shadow space-y-4">
   <div>
  <label class="block text-sm">Section</label>
  <select name="section_id" required class="border px-3 py-2 rounded w-full">
    @foreach($sections as $s)
      <option value="{{ $s->section->id }}">
        {{ $s->section->name }}
      </option>
    @endforeach
  </select>
</div>

<div>
  <label class="block text-sm">Subject</label>
  <select name="subject_id" required class="border px-3 py-2 rounded w-full">
    @foreach($sections as $s)
      <option value="{{ $s->subject->id }}">
        {{ $s->subject->name }}
      </option>
    @endforeach
  </select>
</div>


      <div>
        <label class="block text-sm">Title</label>
        <input name="title" value="{{ old('title') }}" required class="border px-3 py-2 rounded w-full" />
      </div>

      <div>
        <label class="block text-sm">Description</label>
        <textarea name="description" class="border px-3 py-2 rounded w-full">{{ old('description') }}</textarea>
      </div>

      <div class="flex gap-2">
        <div>
          <label class="block text-sm">Total marks</label>
          <input name="total_marks" value="{{ old('total_marks') }}" class="border px-3 py-2 rounded" />
        </div>
        <div>
          <label class="block text-sm">Due at</label>
          <input name="due_at" type="datetime-local" value="{{ old('due_at') }}" class="border px-3 py-2 rounded" />
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
