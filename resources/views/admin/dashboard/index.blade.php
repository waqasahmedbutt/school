@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl md:text-3xl font-bold">Admin Dashboard</h1>
      <p class="text-sm text-gray-500 mt-1">Overview of system metrics and quick actions.</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.classrooms.index') }}" class="px-3 py-2 bg-white border rounded text-sm hover:bg-gray-50">Manage Classrooms</a>
      <a href="{{ route('admin.subjects.index') }}" class="px-3 py-2 bg-white border rounded text-sm hover:bg-gray-50">Manage Subjects</a>
      <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">Manage Users</a>
    </div>
  </div>

  {{-- KPI cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded shadow flex items-center gap-4">
      <div class="p-3 bg-indigo-50 rounded">
        <i class="fas fa-users text-indigo-600 text-xl"></i>
      </div>
      <div>
        <div class="text-xs text-gray-500">Users (total)</div>
        <div class="text-2xl font-semibold">{{ number_format($totalUsers) }}</div>
        <div class="text-xs text-gray-400">Students: {{ $totalStudents }}, Teachers: {{ $totalTeachers }}</div>
      </div>
    </div>

    <div class="bg-white p-4 rounded shadow flex items-center gap-4">
      <div class="p-3 bg-green-50 rounded">
        <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
      </div>
      <div>
        <div class="text-xs text-gray-500">Classrooms</div>
        <div class="text-2xl font-semibold">{{ number_format($totalClassrooms) }}</div>
        <div class="text-xs text-gray-400">Sections & students are managed per classroom.</div>
      </div>
    </div>

    <div class="bg-white p-4 rounded shadow flex items-center gap-4">
      <div class="p-3 bg-yellow-50 rounded">
        <i class="fas fa-book-open text-yellow-600 text-xl"></i>
      </div>
      <div>
        <div class="text-xs text-gray-500">Subjects</div>
        <div class="text-2xl font-semibold">{{ number_format($totalSubjects) }}</div>
        <div class="text-xs text-gray-400">Assigned to teachers per section.</div>
      </div>
    </div>

    <div class="bg-gradient-to-r from-orange-300 to-amber-400 p-4 rounded shadow flex items-center gap-4">

      
      <div>
        <div class="bg-orange-400 text-lg text-gray-800 italic">"Time is what we want most, but what we use worst."</div>
      </div>
    </div>
  </div>

  {{-- Secondary cards --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded shadow">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm text-gray-500">Schools</h3>
          <div class="text-xl font-semibold">{{ number_format($totalSchools) }}</div>
        </div>
        <a href="{{ route('admin.schools.index') ?? '#' }}" class="text-sm text-indigo-600">Manage</a>
      </div>
    </div>

   

    <div class="bg-white p-4 rounded shadow">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm text-gray-500">Parents</h3>
          <div class="text-xl font-semibold">{{ number_format($totalParents) }}</div>
        </div>
        <a href="{{ route('admin.users.index') . '?role=parent' }}" class="text-sm text-indigo-600">View</a>
      </div>
    </div>
  </div>

  {{-- Recent lists --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white p-4 rounded shadow">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-medium">Recent Users</h3>
        <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600">View all</a>
      </div>

      <ul class="space-y-2">
        @forelse($recentUsers as $u)
          <li class="flex items-center justify-between border rounded p-2">
            <div>
              <div class="font-medium">{{ $u->name }}</div>
              <div class="text-xs text-gray-500">{{ $u->email }} • {{ ucfirst($u->role) }}</div>
            </div>
            <div class="text-right text-xs text-gray-500">
              {{ optional($u->created_at)->diffForHumans() }}
            </div>
          </li>
        @empty
          <li class="text-gray-500">No recent users.</li>
        @endforelse
      </ul>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-medium">Recent Classrooms</h3>
        <a href="{{ route('admin.classrooms.index') }}" class="text-sm text-indigo-600">View all</a>
      </div>

      <ul class="space-y-2">
        @forelse($recentClassrooms as $c)
          <li class="flex items-center justify-between border rounded p-2">
            <div>
              <div class="font-medium">{{ $c->name }}</div>
              <div class="text-xs text-gray-500">{{ $c->school->name ?? '—' }}</div>
            </div>
            <div class="text-xs text-gray-500">{{ optional($c->created_at)->diffForHumans() }}</div>
          </li>
        @empty
          <li class="text-gray-500">No recent classrooms.</li>
        @endforelse
      </ul>
    </div>
  </div>

  {{-- CTA / admin quick actions --}}
  <div class="mt-6 bg-white p-4 rounded shadow flex flex-col sm:flex-row gap-3 justify-between items-center">
    <div>
      <h4 class="font-semibold">Quick actions</h4>
      <p class="text-sm text-gray-500">Create resources or run reports.</p>
    </div>

    <div class="flex gap-2">
      <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Add User</a>
      <a href="{{ route('admin.classrooms.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">New Classroom</a>
      <a href="{{ route('admin.subjects.create') }}" class="px-4 py-2 bg-yellow-400 rounded">New Subject</a>
    </div>
  </div>
</div>
@endsection
