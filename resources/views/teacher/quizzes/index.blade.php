@extends('layouts.app')
@section('title','My Quizzes')

@section('content')
<div class="max-w-5xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">❓ My Quizzes</h1>

    {{-- Classroom filter --}}
    <form id="quizzes-filter-form" method="GET" action="{{ route('teacher.quizzes.index') }}" class="flex items-center gap-2">
      <label class="text-sm text-gray-600">Filter</label>
      <select name="classroom_id" class="border px-3 py-2 rounded">
        <option value="">-- All classrooms --</option>
        @foreach($classrooms as $c)
          <option value="{{ $c->id }}" @if(request('classroom_id') == $c->id) selected @endif>{{ $c->name }}</option>
        @endforeach
      </select>
      <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Apply</button>
      @if(request()->filled('classroom_id'))
        <a href="{{ route('teacher.quizzes.index') }}" class="ml-2 text-sm text-gray-600 underline">Clear</a>
      @endif
    </form>
  </div>

  @if($quizzes->isEmpty())
    <div class="bg-white p-6 rounded shadow text-gray-500">You haven't created any quizzes yet.</div>
  @else
    <div class="space-y-3">
      @foreach($quizzes as $q)
        <div class="bg-white p-4 rounded shadow flex justify-between items-center">
          <div>
            <div class="flex items-center gap-2">
              <a href="{{ route('teacher.quizzes.edit', $q) }}" class="font-semibold text-lg text-blue-600">{{ $q->title }}</a>
              @if($q->is_published)
                <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Published</span>
              @else
                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Draft</span>
              @endif
            </div>
            <div class="text-xs text-gray-500 mt-1">{{ $q->classroom->name ?? '—' }} / {{ $q->section->name ?? '—' }} • {{ $q->created_at->format('Y-m-d') }}</div>
            <p class="text-sm text-gray-700 mt-2">{{ \Str::limit($q->description, 200) }}</p>
          </div>

          <div class="flex gap-2">
            <a href="{{ route('teacher.quizzes.edit', $q) }}" class="px-3 py-1 bg-yellow-400 rounded text-sm">Edit</a>
             <a href="{{ route('quizzes.attempts.index', $q) }}" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">
   view Attempts
  </a>
            <form action="{{ route('teacher.quizzes.destroy', $q) }}" method="POST" onsubmit="return confirm('Delete quiz?')">
              @csrf @method('DELETE')
              <button class="px-3 py-1 bg-red-500 text-white rounded text-sm">Delete</button>
            </form>

            @if(!$q->is_published)
              <form action="{{ route('teacher.quizzes.publish', $q) }}" method="POST">
                @csrf
                <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Publish</button>
              </form>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $quizzes->appends(request()->only('classroom_id'))->links() }}
    </div>
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const sel = document.querySelector('#quizzes-filter-form select[name="classroom_id"]');
  sel?.addEventListener('change', () => document.getElementById('quizzes-filter-form').submit());
});
</script>
@endsection
