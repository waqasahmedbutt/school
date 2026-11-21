@extends('layouts.app')
@section('title','My Assignments')

@section('content')
<div class="max-w-5xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">📝 My Assignments</h1>

    {{-- Filter form --}}
    <form id="filter-form" method="GET" action="{{ route('teacher.assignments.index') }}" class="flex items-center gap-2">
      <label class="text-sm text-gray-600 mr-2">Filter by classroom</label>

      <select name="classroom_id" id="classroom-filter" class="border px-3 py-2 rounded">
        <option value="">-- All classrooms --</option>
        @foreach($classrooms as $c)
          <option value="{{ $c->id }}" @if(request('classroom_id') == $c->id) selected @endif>
            {{ $c->name }}
          </option>
        @endforeach
      </select>

      <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Apply</button>

      @if(request()->filled('classroom_id'))
        <a href="{{ route('teacher.assignments.index') }}" class="ml-2 text-sm text-gray-600 underline">Clear</a>
      @endif
    </form>
  </div>

  @if($assignments->isEmpty())
    <div class="bg-white p-6 rounded shadow text-gray-500">No assignments created yet.</div>
  @else
    <div class="space-y-3">
      @foreach($assignments as $a)
        <div class="bg-white p-4 rounded shadow flex justify-between items-center">
          <div>
            <a href="{{ route('teacher.assignments.edit', $a) }}" class="font-semibold text-blue-600 text-lg">{{ $a->title }}</a>
            <div class="text-xs text-gray-500">
              {{ $a->classroom->name ?? '—' }} / {{ $a->section->name ?? '—' }} • 
              Due: {{ $a->due_at ? $a->due_at->format('Y-m-d H:i') : '—' }}
            </div>
            <p class="text-sm text-gray-700 mt-2">{{ \Str::limit($a->description, 220) }}</p>
          </div>

          <div class="flex gap-2">
            <a href="{{ route('teacher.assignments.edit', $a) }}" class="px-3 py-1 bg-yellow-400 rounded text-sm">Edit</a>
            <form action="{{ route('teacher.assignments.destroy', $a) }}" method="POST" onsubmit="return confirm('Delete assignment?')">
              @csrf @method('DELETE')
              <button class="px-3 py-1 bg-red-500 text-white rounded text-sm">Delete</button>
            </form>
            <a href="{{ route('assignments.submissions.index', $a) }}" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">
    Submissions
  </a>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{-- ensure pagination keeps query string --}}
      {{ $assignments->appends(request()->only('classroom_id'))->links() }}
    </div>
  @endif
</div>

{{-- optional: auto-submit on select change --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const sel = document.getElementById('classroom-filter');
  sel?.addEventListener('change', () => {
    // Submit the filter form on change, remove if you prefer manual Apply button
    document.getElementById('filter-form').submit();
  });
});
</script>
@endsection
