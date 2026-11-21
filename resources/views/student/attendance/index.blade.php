@extends('layouts.app')
@section('title','Attendance')

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Attendance</h1>

 <h2>Overall Attendance: {{ $overallPercent }}%</h2>

@foreach($classrooms as $classroom)
  <div class="p-4 border rounded mb-2">
    <h3>{{ $classroom->name }} (Section {{ $classroom->section_id }})</h3>
    <p>Present: {{ $classroom->attendance_present }} / {{ $classroom->attendance_total }}</p>
    <p>Attendance: {{ $classroom->attendance_percent }}%</p>
  </div>
@endforeach
  <div class="grid md:grid-cols-2 gap-4">
    @foreach($classrooms as $c)
      <div class="bg-white p-4 rounded shadow flex justify-between items-center">
        <div>
          <div class="font-medium">{{ $c->name }}</div>
          <div class="text-xs text-gray-500">{{ $c->description }}</div>
        </div>
        <a href="{{ route('student.attendance.detail', ['classroom' => $c->id, 'section' => $classroom->section_id]) }}" class="px-3 py-2 bg-blue-600 text-white rounded">View</a>
      </div>
    @endforeach
  </div>
</div>
@endsection
