@extends('layouts.app')

@section('title','Admin Dashboard')

@section('content')
  <div>
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>
    <p class="mt-2 text-gray-600">Welcome, {{ auth()->user()->name }} — manage users, classrooms, and settings.</p>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <a href="#" class="p-4 bg-white rounded shadow">Manage Users</a>
      <a href="#" class="p-4 bg-white rounded shadow">Classrooms & Sections</a>
      <a href="#" class="p-4 bg-white rounded shadow">Timetables</a>
    </div>
  </div>
@endsection
