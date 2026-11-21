@extends('layouts.app')
@section('title','Student Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 px-4 py-6">

  <!-- Header -->
  <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold text-gray-900">Welcome, {{ auth()->user()->name }}</h1>
      <p class="text-gray-500 mt-1">Here’s what’s happening in your classes today.</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('student.classrooms.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700 transition">📚 My Classes</a>
      <a href="{{ route('student.assignments.index') }}" class="px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 transition">📝 Assignments</a>
    </div>
  </div>

  <!-- Main Grid -->
  <div class="grid md:grid-cols-3 gap-6">

    <!-- Left Column: Reminders & Notifications -->
    <div class="md:col-span-2 space-y-6">
      
      <!-- Reminders -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2"><i class="fas fa-bell text-indigo-600"></i> Reminders</h2>
        @if($assignmentsUpcoming->isEmpty() && $quizzesUpcoming->isEmpty())
          <p class="text-gray-500">No upcoming items.</p>
        @else
          <div class="divide-y divide-gray-200">
            @foreach($assignmentsUpcoming as $a)
              <div class="py-3 hover:bg-gray-50 transition rounded px-2">
                <div class="font-medium text-gray-800">{{ $a->title }} 
                  <span class="text-xs text-gray-500">({{ $a->classroom->name }})</span>
                </div>
                <div class="text-xs text-gray-500 mt-1">Due: {{ $a->due_at ? $a->due_at->format('Y-m-d H:i') : '—' }}</div>
              </div>
            @endforeach

            @foreach($quizzesUpcoming as $q)
              <div class="py-3 hover:bg-gray-50 transition rounded px-2">
                <div class="font-medium text-gray-800">{{ $q->title }} 
                  <span class="text-xs text-gray-500">({{ $q->classroom->name }})</span>
                </div>
                <div class="text-xs text-gray-500 mt-1">Section: {{ $q->section->name ?? '—' }}</div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <!-- Notifications -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2"><i class="fas fa-envelope text-green-600"></i> Notifications</h2>
        @forelse($notifications as $n)
          <div class="py-3 hover:bg-gray-50 transition rounded px-2 border-b last:border-none">
            <div class="font-medium text-gray-800">{{ $n->data['message'] ?? 'Notification' }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $n->created_at->diffForHumans() }}</div>
          </div>
        @empty
          <p class="text-gray-500">No notifications.</p>
        @endforelse
      </div>
    </div>

    <!-- Right Column: Stats -->
    <div class="space-y-6">

      <!-- Stats Cards -->
      <div class="grid gap-4">
        <div class="bg-white shadow rounded-lg p-5 text-center hover:shadow-lg transition">
          <div class="text-sm text-gray-500">Assignment Progress</div>
          <div class="text-3xl font-bold text-indigo-600 mt-1">{{ $assignmentProgress }}%</div>
        </div>

        <div class="bg-white shadow rounded-lg p-5 text-center hover:shadow-lg transition">
          <div class="text-sm text-gray-500">Quiz Progress</div>
          <div class="text-3xl font-bold text-green-600 mt-1">{{ $quizProgress }}%</div>
        </div>

        <div class="bg-white shadow rounded-lg p-5 text-center hover:shadow-lg transition">
          <div class="text-sm text-gray-500">Attendance</div>
          <div class="text-3xl font-bold text-yellow-600 mt-1">{{ $attendancePercent }}%</div>
        </div>
      </div>

      <!-- Quote Card -->
      <div class="bg-white shadow rounded-lg p-5 border-l-4 border-indigo-600">
        <div class="text-sm text-gray-500 mb-2">💡 Quote of the Day</div>
        <div class="italic text-gray-700">"{{ $quote }}"</div>
      </div>

    </div>
  </div>
</div>
@endsection
