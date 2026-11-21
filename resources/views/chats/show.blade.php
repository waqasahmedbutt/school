@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
        <h4 class="mb-0">
            @php
                $others = $chat->users->where('id', '!=', auth()->id())->pluck('name')->join(', ');
            @endphp
            <i class="bi bi-chat-dots"></i>
            Chat with: <span class="fw-bold">{{ $others ?: 'Group/Class' }}</span>
        </h4>
        <a href="{{ route('chats.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Back
        </a>
    </div>

    {{-- Messages --}}
    <div class="card shadow-sm mb-3">
        <div id="chat-box" class="card-body" style="height: 65vh; overflow-y: auto; background: #f9fafb;">
            @if($messages->isEmpty())
                <p class="text-muted text-center my-5">No messages yet. Say hello 👋</p>
            @else
                @foreach($messages as $msg)
                    <div class="mb-3 {{ $msg->sender_id === auth()->id() ? 'text-end' : '' }}">
                        {{-- Meta --}}
                        <div>
                            <small class="text-muted">
                                {{ $msg->sender->name }} • {{ $msg->created_at->format('M d, Y H:i') }}
                            </small>
                        </div>
                        {{-- Bubble --}}
                        <div class="d-inline-block p-2 rounded shadow-sm 
                            {{ $msg->sender_id === auth()->id() 
                                ? 'bg-primary text-white' 
                                : 'bg-light text-dark' }}">
                            {{ $msg->content }}

                            @if($msg->attachment)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $msg->attachment) }}" target="_blank" class="text-decoration-underline">
                                        📎 Attachment
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Message Form --}}
    <form action="{{ route('messages.store', $chat->id) }}" method="POST" enctype="multipart/form-data" class="card shadow-sm">
        @csrf
        <div class="card-body d-flex gap-2 align-items-center">
            <input type="text" name="content" class="form-control" placeholder="Type a message..." value="{{ old('content') }}">
            <input type="file" name="attachment" class="form-control form-control-sm" style="max-width: 200px;">
            <button class="btn btn-success" type="submit">
                Send <i class="bi bi-send"></i>
            </button>
        </div>
    </form>

    {{-- Validation / Flash --}}
    @error('content') 
        <div class="text-danger mt-1">{{ $message }}</div> 
    @enderror
    @error('attachment') 
        <div class="text-danger mt-1">{{ $message }}</div> 
    @enderror

    @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif
</div>

{{-- Auto-scroll to bottom --}}
<script>
    const chatBox = document.getElementById('chat-box');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>
@endsection
