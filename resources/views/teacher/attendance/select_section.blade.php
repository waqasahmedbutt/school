@extends('layouts.app')
@section('title','Select Section')

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Select Section — {{ $classroom->name }}</h1>

  @if($sections->isEmpty())
    <div class="bg-white p-6 rounded shadow text-gray-500">No sections found for this classroom.</div>
  @else
    <div class="grid md:grid-cols-2 gap-4">
      @foreach($sections as $sectionId => $assignments)
        @php
          $section = $assignments->first()->section;
        @endphp
        <div class="bg-white p-4 rounded shadow flex justify-between items-center">
          <div class="font-medium">{{ $section->name }}</div>
          <a href="{{ route('teacher.attendance.records', [$classroom->id, $section->id]) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Select</a>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
