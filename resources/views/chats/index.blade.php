@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Your Chats</h2>

    @if($chats->isEmpty())
        <p>No chats yet.</p>
    @else
        <ul class="list-group">
            @foreach($chats as $chat)
                @php
                    // show other participants (excluding current user)
                    $others = $chat->users->where('id', '!=', auth()->id())->pluck('name')->join(', ');
                    $lastMsg = optional($chat->messages->first())->content;
                @endphp
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $others ?: 'Group / Classroom' }}</strong>
                        <div class="text-muted small">{{ Str::limit($lastMsg, 80) }}</div>
                    </div>
                    <div>
                        <a href="{{ route('chats.show', $chat->id) }}" class="btn btn-sm btn-primary">Open</a>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
