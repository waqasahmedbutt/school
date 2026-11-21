@extends('layouts.app')
@section('title','Create Exam')

@section('content')
<div class="max-w-lg mx-auto">
  <h1 class="text-2xl font-bold mb-4">Create Exam</h1>

  <form action="{{ route('admin.exams.store') }}" method="POST" class="bg-white p-6 shadow rounded">
    @csrf

    <div class="mb-4">
      <label class="block mb-1 font-medium">Title</label>
      <input type="text" name="title" class="w-full border rounded p-2" required>
    </div>

    <div class="mb-4">
      <label class="block mb-1 font-medium">Classroom</label>
      <select name="classroom_id" class="w-full border rounded p-2" required>
        <option value="">Select Classroom</option>
        @foreach($classrooms as $c)
          <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="mb-4">
      <label class="block mb-1 font-medium">Description</label>
      <textarea name="description" class="w-full border rounded p-2"></textarea>
    </div>

    <div class="mb-4">
      <label class="block mb-1 font-medium">Exam Date</label>
      <input type="date" name="exam_date" class="w-full border rounded p-2">
    </div>

    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
  </form>
</div>
@endsection
