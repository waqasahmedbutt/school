@extends('layouts.app')
@section('title','Assignments')

@section('content')
<div class="max-w-6xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">All Assignments</h1>

  <div class="bg-white p-4 rounded shadow overflow-x-auto">
    @if($assignments->isEmpty())
      <p class="text-gray-500">No assignments available.</p>
    @else
      <table class="w-full table-auto border-collapse">
        <thead>
          <tr class="bg-gray-100 text-left">
            <th class="px-4 py-2 border">#</th>
            <th class="px-4 py-2 border">Title</th>
            <th class="px-4 py-2 border">Classroom</th>
            <th class="px-4 py-2 border">Subject</th>
            <th class="px-4 py-2 border">Due Date</th>
            <th class="px-4 py-2 border">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($assignments as $idx => $a)
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-2 border">{{ $idx + 1 }}</td>
              <td class="px-4 py-2 border font-medium">{{ $a->title }}</td>
              <td class="px-4 py-2 border">{{ $a->classroom->name ?? '—' }}</td>
              <td class="px-4 py-2 border">{{ $a->subject->name ?? '—' }}</td>
              <td class="px-4 py-2 border">{{ $a->due_at ? $a->due_at->format('Y-m-d H:i') : '—' }}</td>
              <td class="px-4 py-2 border">
                <a href="{{ route('student.assignments.show', $a) }}" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Open</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-4">
        {{ $assignments->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
