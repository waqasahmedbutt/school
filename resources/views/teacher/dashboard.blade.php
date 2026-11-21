@extends('layouts.app')
@section('title','Teacher Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}</h1>
      <p class="text-sm text-gray-500">Quick overview of your classes and recent notifications.</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('teacher.notifications.index') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Notifications ({{ auth()->user()->unreadNotifications->count() }})</a>
      <a href="{{ route('teacher.classes.index') }}" class="px-3 py-2 bg-gray-100 rounded">My Classes</a>
    </div>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div class="bg-white p-4 rounded shadow">
      <h2 class="font-semibold mb-3">Assigned Classrooms</h2>
      @if($assignments->isEmpty())
        <p class="text-gray-500">You are not assigned to any classroom yet.</p>
      @else
        <ul class="space-y-2">
          @foreach($assignments as $classroomId => $group)
            @php $c = $group->first()->classroom; @endphp
            <li class="flex items-center justify-between border rounded p-3">
              <div>
                <div class="font-medium">{{ $c->name }}</div>
                <div class="text-xs text-gray-500">{{ \Str::limit($c->description, 80) }}</div>
              </div>
              <div class="flex gap-2">
                <a href="{{ route('teacher.classrooms.show', $c) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Open</a>
              </div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>

    <div class="bg-white p-4 rounded shadow">
      <h2 class="font-semibold mb-3">Recent Notifications</h2>
      @if($notifications->isEmpty())
        <p class="text-gray-500">No recent notifications.</p>
      @else
        <ul class="space-y-2">
          @foreach($notifications as $n)
            <li class="p-2 border rounded">
              <div class="flex justify-between items-start">
                <div>
                  <div class="font-medium">{{ $n->data['message'] ?? 'New notification' }}</div>
                  <div class="text-xs text-gray-500">{{ $n->created_at->diffForHumans() }}</div>
                </div>
              </div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</div>
@endsection
