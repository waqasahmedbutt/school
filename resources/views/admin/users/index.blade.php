@extends('layouts.app')
@section('title','Manage Users')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-bold">Users</h1>
  <a href="{{ route('admin.users.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">New User</a>
</div>

<form method="GET" class="mb-4">
  <div class="flex gap-2">
    <select name="role" class="border rounded p-2">
      <option value="">All Roles</option>
      @foreach (['admin','teacher','parent','student'] as $r)
        <option value="{{ $r }}" {{ request('role')===$r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
      @endforeach
    </select>
    <button class="px-3 py-2 bg-gray-800 text-white rounded">Filter</button>
  </div>
</form>

@if(session('success'))
  <div class="mb-3 p-2 bg-green-50 border border-green-200 rounded text-green-700">{{ session('success') }}</div>
@endif

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full">
    <thead class="bg-gray-100 text-left">
      <tr>
        <th class="px-4 py-2">#</th>
        <th class="px-4 py-2">Name</th>
        <th class="px-4 py-2">Email</th>
        <th class="px-4 py-2">Role</th>
        <th class="px-4 py-2">CNIC</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $u)
      <tr class="border-t">
        <td class="px-4 py-2">{{ $u->id }}</td>
        <td class="px-4 py-2">{{ $u->name }}</td>
        <td class="px-4 py-2">{{ $u->email }}</td>
        <td class="px-4 py-2">{{ ucfirst($u->role) }}</td>
        <td class="px-4 py-2">{{ $u->cnic }}</td>
        <td class="px-4 py-2 text-right">
          <a class="px-3 py-1 bg-yellow-500 text-white rounded" href="{{ route('admin.users.edit',$u) }}">Edit</a>
          <form class="inline" method="POST" action="{{ route('admin.users.destroy',$u) }}" onsubmit="return confirm('Delete user?')">
            @csrf @method('DELETE')
            <button class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection
