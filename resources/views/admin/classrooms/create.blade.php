@extends('layouts.app')
@section('title','Create Classroom')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">Create Classroom</h1>
      <p class="text-sm text-gray-500 mt-1">Add a classroom and its sections. Sections can be added/removed below.</p>
    </div>
    <a href="{{ route('admin.classrooms.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-md text-sm hover:bg-gray-200">
      <i class="fas fa-arrow-left text-gray-600"></i> Back to Classrooms
    </a>
  </div>

  <form action="{{ route('admin.classrooms.store') }}" method="POST" class="space-y-6 bg-white shadow rounded-lg p-6">
    @csrf

    {{-- school & name row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">School</label>
        <select name="school_id" required class="mt-1 block w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-400">
          <option value="">— Select school —</option>
          @foreach($schools as $school)
            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
              {{ $school->name }}
            </option>
          @endforeach
        </select>
        @error('school_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input name="name" value="{{ old('name') }}" placeholder="e.g. Grade 7 - A" class="mt-1 block w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-400" required />
        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- description & capacity --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="3" class="mt-1 block w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-400">{{ old('description') }}</textarea>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Capacity</label>
        <input name="capacity" value="{{ old('capacity') }}" type="number" min="0" class="mt-1 block w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-400" />
      </div>
    </div>

    {{-- sections --}}
    <div>
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-semibold text-gray-800">Sections</h3>
        <p class="text-xs text-gray-500">Add one or more sections for this classroom.</p>
      </div>

      <div id="sections-wrapper" class="space-y-3"></div>

      <div class="mt-3">
        <button type="button" id="add-section" class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          <i class="fas fa-plus"></i> Add Section
        </button>
      </div>
    </div>

    {{-- errors summary --}}
    @if($errors->any())
      <div class="rounded-md bg-red-50 p-3 text-sm text-red-700 border border-red-100">
        <ul class="list-disc list-inside">
          @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <div class="flex justify-end gap-3">
      <a href="{{ route('admin.classrooms.index') }}" class="px-4 py-2 bg-gray-100 rounded-md">Cancel</a>
      <button class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create Classroom</button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const wrapper = document.getElementById('sections-wrapper');
  const addBtn = document.getElementById('add-section');

  function newSectionRow(id = null, name = '', code = '') {
    const idx = Date.now() + Math.floor(Math.random()*1000);
    const row = document.createElement('div');
    row.className = 'grid grid-cols-1 md:grid-cols-4 gap-3 items-center bg-gray-50 border p-3 rounded';
    row.innerHTML = `
      <div class="md:col-span-2">
        <label class="sr-only">Section name</label>
        <input type="text" name="sections[${idx}][name]" placeholder="Section name (e.g. A)" value="${name}" class="w-full border rounded p-2" required />
      </div>
      <div>
        <label class="sr-only">Code</label>
        <input type="text" name="sections[${idx}][code]" placeholder="Code (optional)" value="${code}" class="w-full border rounded p-2" />
      </div>
      <div class="flex gap-2 justify-end">
        <button type="button" class="remove-section inline-flex items-center gap-2 px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
          <i class="fas fa-trash"></i> Remove
        </button>
      </div>
    `;
    wrapper.appendChild(row);

    row.querySelector('.remove-section').addEventListener('click', () => row.remove());
  }

  addBtn.addEventListener('click', () => newSectionRow());
  // add a default one
  newSectionRow();
});
</script>
@endsection
