@extends('layouts.app')

@section('title', 'Edit Subject — ' . ($subject->name ?? ''))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Edit Subject</h1>
    <a href="{{ route('admin.subjects.index') }}" class="text-sm px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">Back to list</a>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 rounded bg-green-50 text-green-800">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="mb-4 p-3 rounded bg-red-50 text-red-800">
      <ul class="list-disc list-inside text-sm">
        @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.subjects.update', $subject) }}" method="POST" class="bg-white border rounded p-6 shadow">
    @csrf
    @method('PUT')

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700">Name</label>
      <input name="name" value="{{ old('name', $subject->name) }}" required
             class="mt-1 block w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
      @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700">Code (optional)</label>
      <input name="code" value="{{ old('code', $subject->code) }}"
             class="mt-1 block w-48 border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
      @error('code') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700">Description</label>
      <textarea name="description" rows="4" class="mt-1 block w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('description', $subject->description) }}</textarea>
      @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex justify-end gap-3">
      <a href="{{ route('admin.subjects.index') }}" class="px-4 py-2 bg-gray-100 rounded">Cancel</a>
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save Changes</button>
    </div>
  </form>
</div>
@endsection
