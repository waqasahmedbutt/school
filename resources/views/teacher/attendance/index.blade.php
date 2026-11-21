@extends('layouts.app')
@section('title','Attendance')

@section('content')
<div class="max-w-5xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">📋 Attendance</h1>
  <p class="text-gray-500 mb-4">Select a classroom to continue.</p>

  @if($classrooms->isEmpty())
    <div class="bg-white p-6 rounded shadow text-gray-500">You are not assigned to any classroom.</div>
  @else
    <div class="grid md:grid-cols-2 gap-4">
      @foreach($classrooms as $classroom)
        <div class="bg-white p-4 rounded shadow flex justify-between items-center">
          <div class="font-medium">{{ $classroom->name }}</div>
          <a href="{{ route('teacher.attendance.selectSection', $classroom->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Select</a>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
