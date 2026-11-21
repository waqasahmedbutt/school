@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Parent Chat</h4>
    <p class="text-muted">Select a child and classroom to start chatting with teachers.</p>

    <div class="row">
        @forelse($children as $child)
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $child->name }}</h5>

                        @if($child->classrooms->isNotEmpty())
                            <ul class="list-group list-group-flush">
                                @foreach($child->classrooms as $classroom)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $classroom->name }}
                                        <a href="{{ route('parent.chat.classroom', $classroom->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            Open
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted mb-0">No classrooms assigned.</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">You don’t have any children registered in classrooms.</p>
        @endforelse
    </div>
</div>
@endsection
