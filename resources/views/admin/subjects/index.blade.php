@extends('layouts.app')
@section('title','Subjects')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-bold">Subjects</h1>
  <a href="{{ route('admin.subjects.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">New Subject</a>
</div>

@if(session('success'))
  <div class="mb-3 p-2 bg-green-50 border border-green-200 text-green-700 rounded">{{ session('success') }}</div>
@endif

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full">
    <thead class="bg-gray-100 text-left">
      <tr>
        <th class="px-4 py-2">#</th>
        <th class="px-4 py-2">Name</th>
        <th class="px-4 py-2">Code</th>
        <th class="px-4 py-2">Description</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($subjects as $s)
      <tr class="border-t">
        <td class="px-4 py-2">{{ $s->id }}</td>
        <td class="px-4 py-2">{{ $s->name }}</td>
        <td class="px-4 py-2">{{ $s->code }}</td>
        <td class="px-4 py-2">{{ Str::limit($s->description, 60) }}</td>
        <td class="px-4 py-2 text-right">
          <a href="{{ route('admin.subjects.edit',$s) }}" class="px-3 py-1 bg-yellow-500 text-white rounded">Edit</a>
          <form class="inline" method="POST" action="{{ route('admin.subjects.destroy',$s) }}" onsubmit="return confirm('Delete subject?')">
            @csrf @method('DELETE')
            <button class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $subjects->links() }}</div>
@endsection
