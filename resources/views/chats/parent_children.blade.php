@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h2 class="text-xl font-bold mb-4">💬 Chat with Teachers</h2>
    <p class="text-gray-600 mb-6">Select a child to continue.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse ($children as $child)
            @foreach($child->classrooms as $classroom)
                <a href="{{ route('parent.chat.teachers', [$child->id, $classroom->id]) }}" 
                   class="block p-4 rounded-lg shadow hover:bg-blue-50 transition border">
                    <div class="font-semibold text-lg">{{ $child->name }}</div>
                    <div class="text-sm text-gray-500">
                        Classroom: {{ $classroom->name }}
                    </div>
                </a>
            @endforeach
        @empty
            <div class="col-span-2 text-center text-gray-500">
                No children found in any classrooms.
            </div>
        @endforelse
    </div>
</div>
@endsection
