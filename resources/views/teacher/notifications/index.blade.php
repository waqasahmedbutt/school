@extends('layouts.app')
@section('title','Notifications')
@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Notifications</h1>

  <div class="bg-white p-4 rounded shadow">
    @forelse($notifications as $n)
  <div class="border-b py-2 flex justify-between items-start">
    <div>
      <div class="font-medium">{{ $n->data['message'] ?? 'Notification' }}</div>
      <div class="text-xs text-gray-500">{{ $n->created_at->diffForHumans() }}</div>
    </div>

    <form method="POST" action="{{ route('teacher.notifications.read', $n->id) }}">
      @csrf
      <button class="px-2 py-1 bg-blue-600 text-white rounded text-sm">Mark read</button>
    </form>
  </div>
@empty
  <p class="text-gray-500">No unread notifications 🎉</p>
@endforelse


    <div class="mt-3">
      {{ $notifications->links() }}
    </div>
  </div>
</div>
@endsection
