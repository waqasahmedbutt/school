@extends('layouts.app')
@section('title','My Classrooms')

@section('content')
<div class="max-w-6xl mx-auto">
  {{-- Motivational header --}}
  <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 rounded-xl shadow mb-6">
    <h1 class="text-3xl font-bold">📚 My Classrooms</h1>
    <p class="text-sm opacity-90 mt-1">"Education is the most powerful weapon which you can use to change the world." — Nelson Mandela</p>
  </div>

  <div class="grid md:grid-cols-3 gap-6">
    @forelse($classrooms as $c)
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
        <div class="font-semibold text-lg mb-1">{{ $c->name }}</div>
        <div class="text-sm text-gray-600">{{ $c->description }}</div>
        <div class="mt-4 flex justify-end">
          <a href="{{ route('student.classrooms.show', $c) }}" 
             class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm shadow">
            Open →
          </a>
        </div>
      </div>
    @empty
      <div class="col-span-3 text-center text-gray-500 py-10">
        <p>No classrooms yet. 🚀</p>
      </div>
    @endforelse
  </div>
</div>
@endsection
