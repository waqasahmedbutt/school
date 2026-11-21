@extends('layouts.app')
@section('title','Edit Classroom')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">Edit Classroom</h1>
      <p class="text-sm text-gray-500 mt-1">Modify classroom details and manage sections.</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admin.classrooms.index') }}" class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">Back</a>
      <a href="{{ route('admin.classrooms.show', $classroom) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">View</a>
    </div>
  </div>

  <form action="{{ route('admin.classrooms.update', $classroom) }}" method="POST" class="space-y-6 bg-white shadow rounded-lg p-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">School</label>
        <select name="school_id" required class="mt-1 block w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-400">
          <option value="">— Select school —</option>
          @foreach($schools as $school)
            <option value="{{ $school->id }}" {{ old('school_id', $classroom->school_id) == $school->id ? 'selected' : '' }}>
              {{ $school->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input name="name" value="{{ old('name', $classroom->name) }}" class="mt-1 block w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-400" required />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="3" class="mt-1 block w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-400">{{ old('description', $classroom->description) }}</textarea>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Capacity</label>
        <input name="capacity" value="{{ old('capacity', $classroom->capacity) }}" type="number" min="0" class="mt-1 block w-full border rounded-md p-2 focus:ring-2 focus:ring-indigo-400" />
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-semibold text-gray-800">Sections</h3>
        <p class="text-xs text-gray-500">Add or remove sections below.</p>
      </div>

      <div id="sections-wrapper" class="space-y-3">
        @foreach($classroom->sections as $s)
          <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-center bg-gray-50 border p-3 rounded" data-existing-id="{{ $s->id }}">
            <input type="hidden" name="sections[{{ $s->id }}][id]" value="{{ $s->id }}">
            <div class="md:col-span-2">
              <label class="sr-only">Section name</label>
              <input type="text" name="sections[{{ $s->id }}][name]" value="{{ old('sections.'.$s->id.'.name', $s->name) }}" class="w-full border rounded p-2" required />
            </div>
            <div>
              <label class="sr-only">Code</label>
              <input type="text" name="sections[{{ $s->id }}][code]" value="{{ old('sections.'.$s->id.'.code', $s->code) }}" class="w-full border rounded p-2" />
            </div>
            <div class="flex justify-end gap-2">
              <button type="button" class="remove-section inline-flex items-center gap-2 px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                <i class="fas fa-trash"></i> Remove
              </button>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-3">
        <button type="button" id="add-section" class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          <i class="fas fa-plus"></i> Add Section
        </button>
      </div>
    </div>

    <div class="flex justify-end gap-3">
      <a href="{{ route('admin.classrooms.index') }}" class="px-4 py-2 bg-gray-100 rounded-md">Cancel</a>
      <button class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">Update Classroom</button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const wrapper = document.getElementById('sections-wrapper');
  const addBtn = document.getElementById('add-section');

  function newSectionRow(id = null, name = '', code = '') {
    const idx = id ?? (Date.now() + Math.floor(Math.random()*1000));
    const row = document.createElement('div');
    row.className = 'grid grid-cols-1 md:grid-cols-4 gap-3 items-center bg-gray-50 border p-3 rounded';
    row.innerHTML = `
      ${ id ? `<input type="hidden" name="sections[${id}][id]" value="${id}">` : '' }
      <div class="md:col-span-2">
        <input type="text" name="sections[${idx}][name]" placeholder="Section name (e.g. A)" value="${name}" class="w-full border rounded p-2" required />
      </div>
      <div>
        <input type="text" name="sections[${idx}][code]" placeholder="Code (optional)" value="${code}" class="w-full border rounded p-2" />
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" class="remove-section inline-flex items-center gap-2 px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
          <i class="fas fa-trash"></i> Remove
        </button>
      </div>
    `;
    wrapper.appendChild(row);

    row.querySelector('.remove-section').addEventListener('click', () => row.remove());
  }

  addBtn.addEventListener('click', () => newSectionRow());

  // wire existing remove buttons
  document.querySelectorAll('.remove-section').forEach(btn => {
    btn.addEventListener('click', function() {
      const row = this.closest('div.grid');
      row.remove();
    });
  });
});
</script>
@endsection
