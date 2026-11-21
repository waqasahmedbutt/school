@extends('layouts.app')
@section('title','Edit Timetable Entry')

@section('content')
<div class="max-w-2xl">
  <h1 class="text-2xl font-bold mb-4">✏️ Edit Period</h1>

  @if($errors->any())
    <div class="mb-3 p-2 bg-red-50 border border-red-200 text-red-700 rounded">
      <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form action="{{ route('admin.timetable.update', ['timetable' => $timetable->id]) }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow">
    @csrf @method('PUT')

    <div>
      <label class="block text-sm font-medium">Classroom & Section</label>
      <select name="section_id" class="border rounded p-2 w-full">
        @foreach($classrooms as $c)
          <optgroup label="{{ $c->name }}">
            @foreach($c->sections as $s)
              <option value="{{ $s->id }}" @selected($timetable->section_id == $s->id)>Section {{ $s->name }}</option>
            @endforeach
          </optgroup>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium">Subject</label>
      <select name="subject_id" class="border rounded p-2 w-full">
        @foreach($subjects as $sub)
          <option value="{{ $sub->id }}" @selected($timetable->subject_id == $sub->id)>{{ $sub->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium">Teacher</label>
      <select name="teacher_id" class="border rounded p-2 w-full">
        @foreach($teachers as $t)
          <option value="{{ $t->id }}" @selected($timetable->teacher_id == $t->id)>{{ $t->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium">Day</label>
      <select name="day_of_week" class="border rounded p-2 w-full">
        @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $index => $d)
          <option value="{{ $index }}" @selected($timetable->day_of_week == $index)>{{ $d }}</option>
        @endforeach
      </select>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium">Start Time</label>
        <input type="time" name="start_time" value="{{ \Carbon\Carbon::parse($timetable->start_time)->format('H:i') }}" class="border p-2 rounded w-full" required>
      </div>
      <div>
        <label class="block text-sm font-medium">End Time</label>
        <input type="time" name="end_time" value="{{ \Carbon\Carbon::parse($timetable->end_time)->format('H:i') }}" class="border p-2 rounded w-full" required>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">Room</label>
      <input type="text" name="room" value="{{ $timetable->room }}" class="border p-2 rounded w-full" placeholder="optional">
    </div>

    <div class="flex gap-2">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
      <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
    </div>
  </form>
</div>
@endsection
