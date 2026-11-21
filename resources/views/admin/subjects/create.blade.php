@extends('layouts.app')
@section('title','Create Subject')
@section('content')
<h1 class="text-2xl font-bold mb-4">Create Subject</h1>

@if($errors->any())
  <div class="mb-3 p-2 bg-red-50 border border-red-200 rounded text-red-700">
    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

<form method="POST" action="{{ route('admin.subjects.store') }}" class="bg-white p-6 rounded shadow space-y-4">
  @csrf
  <div>
    <label class="block text-sm font-medium">Name</label>
    <input name="name" class="border rounded p-2 w-full" required>
  </div>
  <div class="grid sm:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium">Code</label>
      <input name="code" class="border rounded p-2 w-full">
    </div>
    <div>
      <label class="block text-sm font-medium">Description</label>
      <input name="description" class="border rounded p-2 w-full">
    </div>
  </div>

  <div class="flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    <a class="px-4 py-2 bg-gray-200 rounded" href="{{ route('admin.subjects.index') }}">Cancel</a>
  </div>
</form>
@endsection
