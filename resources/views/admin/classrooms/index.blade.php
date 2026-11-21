@extends('layouts.app')
@section('title', 'Classrooms')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
  <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">Classrooms</h1>
      <p class="text-sm text-gray-500 mt-1">Manage classrooms and their sections.</p>
    </div>

    <div class="flex items-center gap-3 w-full md:w-auto">
      <form method="GET" class="flex items-center gap-3 w-full md:w-auto">
        <select name="school_id" class="border rounded p-2 text-sm">
          <option value="">All schools</option>
          @foreach($schools as $school)
            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
          @endforeach
        </select>

        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or description" class="border rounded p-2 w-full md:w-80" />

        <button type="submit" class="px-3 py-2 bg-gray-800 text-white rounded">Search</button>
      </form>

      <a href="{{ route('admin.classrooms.create') }}" class="ml-2 px-4 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700">
        <i class="fas fa-plus mr-2"></i> New Classroom
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 rounded bg-green-50 text-green-800">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="mb-4 p-3 rounded bg-red-50 text-red-800">{{ session('error') }}</div>
  @endif

  <div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sections</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($classrooms as $classroom)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ $classroom->name }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ optional($classroom->school)->name ?? '—' }}</div>
              </td>

              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ $classroom->school->name ?? '—' }}
              </td>

              <td class="px-4 py-4 break-words text-sm text-gray-600" style="max-width: 28rem;">
                {{ \Str::limit($classroom->description, 140) }}
              </td>

              <td class="px-4 py-4 whitespace-nowrap">
                @if($classroom->sections->isEmpty())
                  <span class="text-xs text-gray-500">No sections</span>
                @else
                  <div class="flex flex-wrap gap-1">
                    @foreach($classroom->sections as $s)
                      <a href="{{ route('admin.classrooms.sections.show', [$classroom->id, $s->id]) }}"
                         class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700">
                        {{ $s->name }}
                      </a>
                    @endforeach
                  </div>
                @endif
              </td>

              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ $classroom->capacity ?? '—' }}
              </td>

              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ optional($classroom->created_at)->format('d M, Y') ?? '—' }}
              </td>

              <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="inline-flex items-center gap-2">
                  <a href="{{ route('admin.classrooms.show', $classroom) }}" class="px-2 py-1 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700">View</a>
                  <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="px-2 py-1 bg-yellow-400 rounded text-xs">Edit</a>

                  <form action="{{ route('admin.classrooms.destroy', $classroom) }}" method="POST" onsubmit="return confirm('Delete classroom?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded text-xs">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                No classrooms found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 bg-gray-50 sm:flex sm:items-center sm:justify-between">
      <div class="text-sm text-gray-700">
        Showing <span class="font-medium">{{ $classrooms->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $classrooms->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $classrooms->total() ?? 0 }}</span> results
      </div>

      <div class="mt-3 sm:mt-0">
        {{ $classrooms->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
