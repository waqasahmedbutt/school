@extends('layouts.app')
@section('title','My Classrooms')

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">My Classrooms (by Section)</h1>
    <a href="{{ route('teacher.dashboard') }}" class="px-3 py-2 bg-gray-100 rounded">Back to Dashboard</a>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    @forelse($classroomSections as $cs)
      <div class="bg-white p-4 rounded shadow">
        <div class="flex justify-between items-start">
          <div>
            <h2 class="font-semibold text-lg">{{ $cs->classroom->name }} <span class="text-sm text-gray-500">/ Section {{ $cs->section->name ?? '—' }}</span></h2>
            <p class="text-xs text-gray-500 mt-1">{{ \Str::limit($cs->classroom->description ?? '', 120) }}</p>

            <div class="mt-3 text-xs">
              <div class="flex flex-wrap gap-2 items-center">
                <span class="text-xs text-gray-500 mr-2">Subjects:</span>
                @forelse($cs->subjects as $sub)
                  <span class="inline-block px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-xs">{{ $sub->name ?? '—' }}</span>
                @empty
                  <span class="text-xs text-gray-400">No subjects assigned</span>
                @endforelse
              </div>

              <div class="mt-2 text-xs text-gray-600">Students: <strong>{{ $cs->students_count }}</strong></div>
            </div>
          </div>

          <div class="flex flex-col gap-2 items-end">
            {{-- open classroom + section: pass section_id as query so classroomShow picks it --}}
            <a href="{{ route('teacher.classrooms.show', [$cs->classroom->id, 'section_id' => $cs->section->id]) }}"
               class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Open</a>

            {{-- quick links: assignments / quizzes filtered by classroom+section (optional) --}}
            <a href="{{ route('teacher.assignments.index', ['classroom' => $cs->classroom->id, 'section_id' => $cs->section->id]) }}"
               class="px-3 py-1 bg-gray-100 rounded text-sm">Assignments</a>
            <a href="{{ route('teacher.quizzes.index', ['classroom' => $cs->classroom->id, 'section_id' => $cs->section->id]) }}"
               class="px-3 py-1 bg-gray-100 rounded text-sm">Quizzes</a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-span-2 bg-white p-4 rounded shadow text-gray-500">You are not assigned to any classrooms/sections.</div>
    @endforelse
  </div>
</div>
@endsection
