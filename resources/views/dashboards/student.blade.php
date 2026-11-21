@extends('layouts.app')

@section('title','Student Dashboard')

@section('content')
  <div>
    <h1 class="text-2xl font-bold">Student Dashboard</h1>
    <p class="mt-2 text-gray-600">Welcome, {{ auth()->user()->name }} — view assignments, quizzes and attendance here.</p>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
      <a href="#" class="p-4 bg-white rounded shadow">My Assignments</a>
      <a href="#" class="p-4 bg-white rounded shadow">My Quizzes</a>
    </div>
  </div>
@endsection
