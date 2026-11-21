@extends('layouts.app')

@section('title','Parent Dashboard')

@section('content')
  <div>
    <h1 class="text-2xl font-bold">Parent Dashboard</h1>
    <p class="mt-2 text-gray-600">Welcome, {{ auth()->user()->name }} — view your children's progress and marks.</p>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
      <a href="#" class="p-4 bg-white rounded shadow">My Children</a>
      <a href="#" class="p-4 bg-white rounded shadow">Marksheets</a>
    </div>
  </div>
@endsection
