@extends('layouts.app')
@section('title', 'Classroom Details')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">{{ $classroom->name }}</h1>
      <p class="text-sm text-gray-500 mt-1">School: <span class="font-medium text-gray-700">{{ $classroom->school->name ?? '—' }}</span></p>
      <p class="text-sm text-gray-500 mt-1">Capacity: <span class="font-medium text-gray-700">{{ $classroom->capacity ?? '—' }}</span></p>
    </div>

    <div class="flex gap-2">
      <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="px-3 py-2 bg-yellow-400 rounded">Edit</a>
      <a href="{{ route('admin.classrooms.index') }}" class="px-3 py-2 bg-gray-100 rounded">Back</a>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-4">
      <div class="bg-white p-5 rounded shadow">
        <h2 class="font-semibold text-lg mb-2">Overview</h2>
        <p class="text-gray-700">{{ $classroom->description ?? 'No description provided.' }}</p>
      </div>

      <div class="bg-white p-5 rounded shadow">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold text-lg">Sections</h3>
          <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="text-sm text-indigo-600">Manage</a>
        </div>

        @if($classroom->sections->isEmpty())
          <p class="text-gray-500">No sections. Add sections in Edit.</p>
        @else
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($classroom->sections as $section)
              <div class="border rounded p-3 flex justify-between items-start">
                <div>
                  <h4 class="font-medium">{{ $section->name }}</h4>
                  <p class="text-xs text-gray-500 mt-1">Code: {{ $section->code ?? '—' }}</p>
                </div>
                <div class="flex gap-2">
                  <a href="{{ route('admin.classrooms.sections.show', [$classroom->id, $section->id]) }}" class="px-2 py-1 bg-indigo-600 text-white rounded text-sm">View</a>
                  <form action="{{ route('admin.classrooms.sections.destroy', [$classroom->id, $section->id]) }}" method="POST" onsubmit="return confirm('Delete section? This will remove students & teacher assignments for this section.')">
                    @csrf
                    @method('DELETE')
                    <button class="px-2 py-1 bg-red-500 text-white rounded text-sm">Delete</button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <aside class="space-y-4">
      <div class="bg-white p-4 rounded shadow text-sm">
        <h4 class="font-semibold mb-2">Meta</h4>
        <p class="text-gray-600">Created at: <span class="font-medium text-gray-800">{{ optional($classroom->created_at)->format('d M, Y') ?? '—' }}</span></p>
        <p class="text-gray-600 mt-1">Sections: <span class="font-medium text-gray-800">{{ $classroom->sections->count() }}</span></p>
      </div>

      <div class="bg-white p-4 rounded shadow text-sm">
        <h4 class="font-semibold mb-2">Quick Actions</h4>
        <div class="flex flex-col gap-2">
          <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="px-3 py-2 bg-yellow-400 rounded text-center">Edit Classroom</a>
          <a href="{{ route('admin.classrooms.index') }}" class="px-3 py-2 bg-gray-100 rounded text-center">All Classrooms</a>
        </div>
      </div>
    </aside>
  </div>
</div>

@endsection
