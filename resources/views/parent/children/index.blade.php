@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Your Children</h1>

    @if($children->isEmpty())
        <div class="p-4 bg-yellow-50 border">No children linked to your account.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($children as $child)
                <a href="{{ route('parent.child.overview', $child->id) }}" class="block p-4 border rounded hover:shadow">
                    <div class="font-medium text-lg">{{ $child->name }}</div>
                    <div class="text-sm text-gray-500">{{ $child->email }}</div>
                    <div class="mt-2 text-sm text-gray-600">Classrooms:
                        @foreach($child->classrooms as $c) <span class="inline-block px-2 py-1 ml-1 bg-gray-100 rounded">{{ $c->name ?? 'Classroom '.$c->id }}</span> @endforeach
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
