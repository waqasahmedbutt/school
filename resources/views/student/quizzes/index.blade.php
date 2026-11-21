@extends('layouts.app')
@section('title','Quizzes')

@section('content')
<div class="max-w-6xl mx-auto px-4">
  <h1 class="text-3xl font-bold mb-6 text-gray-800">📚 Available Quizzes</h1>

  @if($quizzes->isEmpty())
    <div class="p-6 bg-gray-100 text-center rounded-lg shadow">
      <p class="text-gray-500">No quizzes available right now.</p>
    </div>
  @else
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      @foreach($quizzes as $q)
        <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-5 flex flex-col justify-between">
          <div>
            <h2 class="font-semibold text-lg text-gray-800">{{ $q->title }}</h2>
            <p class="text-sm text-gray-500 mt-1">
              {{ $q->classroom->name }}
              @if($q->section) • {{ $q->section->name }} @endif
              @if($q->subject) • {{ $q->subject->name }} @endif
            </p>
            <p class="text-xs text-gray-400 mt-1">By {{ $q->creator->name ?? 'Unknown' }}</p>
            <p class="mt-2 text-sm text-gray-600">⏱ {{ $q->duration_minutes ?? 'Unlimited' }} minutes</p>
          </div>
          <a href="{{ route('student.quizzes.show', $q) }}"
             class="mt-4 inline-block text-center px-4 py-2 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-lg text-white font-medium hover:scale-105 transition">
            Start
          </a>
        </div>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $quizzes->links() }}
    </div>
  @endif
</div>
@endsection
