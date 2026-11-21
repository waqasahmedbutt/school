@extends('layouts.app')
@section('title','Create User')
@section('content')
<h1 class="text-2xl font-bold mb-4">Create User</h1>

@if($errors->any())
  <div class="mb-3 p-2 bg-red-50 border border-red-200 rounded text-red-700">
    <ul class="list-disc list-inside">
      @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('admin.users.store') }}"
      class="bg-white p-6 rounded shadow space-y-4"
      x-data="{ role: '{{ old('role','admin') }}' }">
  @csrf

  <div>
    <label class="block text-sm font-medium">Name</label>
    <input name="name" value="{{ old('name') }}" class="border rounded p-2 w-full" required>
  </div>

  <div>
    <label class="block text-sm font-medium">Email</label>
    <input type="email" name="email" value="{{ old('email') }}" class="border rounded p-2 w-full" required>
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium">Password</label>
      <input type="password" name="password" class="border rounded p-2 w-full" required>
    </div>
    <div>
      <label class="block text-sm font-medium">Role</label>
      <select name="role" x-model="role" class="border rounded p-2 w-full" required>
        @foreach (['admin','teacher','parent','student'] as $r)
          <option value="{{ $r }}" @selected(old('role')===$r)>{{ ucfirst($r) }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <!-- Parent CNIC -->
  <div x-show="role === 'parent'">
    <label class="block text-sm font-medium">Parent CNIC</label>
    <input name="cnic"
           value="{{ old('cnic') }}"
           placeholder="12345-1234567-1"
           class="border rounded p-2 w-full"
           x-bind:required="role === 'parent'"
           x-bind:disabled="role !== 'parent'">
  </div>

  <!-- Student CNIC -->
  <div x-show="role === 'student'">
    <label class="block text-sm font-medium">Student CNIC</label>
    <input name="cnic"
           value="{{ old('cnic') }}"
           placeholder="12345-1234567-1"
           class="border rounded p-2 w-full"
           x-bind:required="role === 'student'"
           x-bind:disabled="role !== 'student'">
  </div>

  <!-- Student's Parent CNIC -->
  <div x-show="role === 'student'">
    <label class="block text-sm font-medium">Parent CNIC (must exist)</label>
    <input name="parent_cnic"
           value="{{ old('parent_cnic') }}"
           placeholder="12345-1234567-1"
           class="border rounded p-2 w-full"
           x-bind:required="role === 'student'"
           x-bind:disabled="role !== 'student'">
  </div>

  <div class="flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    <a class="px-4 py-2 bg-gray-200 rounded" href="{{ route('admin.users.index') }}">Cancel</a>
  </div>
</form>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection
