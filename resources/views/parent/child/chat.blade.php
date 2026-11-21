@extends('layouts.app')

@section('content')
{{-- Reuse your chat CSS from the students view. If you put that CSS into a partial, include it. --}}
@includeIf('chats._styles') {{-- optional: extract styles into partial to avoid duplication --}}

<div class="chat-wrapper">
    {{-- Sidebar --}}
    <div class="chat-sidebar">
        <div class="search d-flex align-items-start px-3 py-2">
            <div>
                <div style="font-weight:700">Teachers</div>
               
            </div>
        </div>

        <div class="px-3 py-2">
            <input id="teacher-search" class="form-control form-control-sm" placeholder="Search teachers...">
        </div>

        <div class="chat-users">
            @forelse($teachers as $teacher)
                @php
                    $isActive = isset($activeTeacher) && $activeTeacher && $activeTeacher->id === $teacher->id;
                    $link = route('chats.parents.teachers', [$classroom->id, $sectionId ?? null]) . '?user=' . $teacher->id;
                    $seed = 'seed-' . (($loop->index % 3) + 1);

                    // subjects maned via teacherAssignments relation
                    $subjects = collect();
                    if ($teacher->relationLoaded('teacherAssignments')) {
                        $teacher->teacherAssignments->each(function($assign) use (&$subjects) {
                            if (isset($assign->subject)) $subjects->push($assign->subject->name);
                            elseif (!empty($assign->subject_name)) $subjects->push($assign->subject_name); // fallback
                        });
                    }
                    $subjects = $subjects->unique()->values();
                @endphp

                <a href="{{ $link }}" class="chat-user d-flex align-items-center {{ $isActive ? 'active' : '' }}" data-name="{{ strtolower($teacher->name) }}">
                    <div class="avatar {{ $seed }}">{{ strtoupper(substr($teacher->name,0,1)) }}</div>

                    <div class="user-meta" style="flex:1; min-width:0;">
                        <div class="name truncate">{{ $teacher->name }}</div>

                        {{-- child list for this parent in this classroom --}}
                        <div class="sub truncate">
                            <small class="text-muted">
                                @if($children->isNotEmpty())
                                    Your child(ren):
                                    {{ $children->pluck('name')->join(', ') }}
                                @else
                                    No child in this classroom
                                @endif
                            </small>
                        </div>

                        {{-- subjects badges --}}
                        @if($subjects->isNotEmpty())
                            <div class="mt-1">
                                @foreach($subjects as $sub)
                                    <span class="badge bg-secondary me-1">{{ $sub }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- optional unread badge --}}
                    @if(!empty($teacher->unread_count) && $teacher->unread_count > 0)
                        <div class="badge bg-success ms-2">{{ $teacher->unread_count }}</div>
                    @endif
                </a>
            @empty
                <div class="p-3 text-muted">No teachers assigned to this classroom.</div>
            @endforelse
        </div>
    </div>

    {{-- Main chat (reuse same structure as other chat views) --}}
    <div class="chat-main">
        <div class="chat-header">
            <div class="title">
                @if(isset($activeTeacher))
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                        {{ strtoupper(substr($activeTeacher->name,0,1)) }}
                    </div>
                    <div style="margin-left:8px;">
                        <div class="name">{{ $activeTeacher->name }}</div>
                        <div class="small text-muted">Teacher • Chat about {{ $classroom->name }}</div>
                    </div>
                @else
                    <div>
                        <div class="name">Select a teacher</div>
                        <div class="small text-muted">Choose a teacher from the left to open chat</div>
                    </div>
                @endif
            </div>

            <div class="actions d-flex align-items-center gap-2">
                <a href="{{ route('chats.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
                @if(isset($activeTeacher))
                    <a href="#" class="btn btn-outline-secondary btn-sm">Profile</a>
                @endif
            </div>
        </div>

        <div class="messages" id="chat-box">
            @if(isset($chat) && $messages->isNotEmpty())
                @foreach($messages as $msg)
                    @php $isMe = $msg->sender_id === auth()->id(); @endphp
                    <div class="msg-row {{ $isMe ? 'me' : 'them' }}">
                        <div>
                            <div class="bubble {{ $isMe ? 'me' : 'them' }}">
                                @if(!$isMe)
                                    <div class="small text-muted" style="margin-bottom:6px;">{{ $msg->sender->name }}</div>
                                @endif

                                <div class="msg-content">{{ $msg->content }}</div>

                                @if($msg->attachment)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $msg->attachment) }}" target="_blank" class="small">📎 View attachment</a>
                                    </div>
                                @endif

                                <div class="msg-meta text-end">{{ $msg->created_at->format('H:i') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @elseif(isset($chat))
                <div class="d-flex justify-content-center align-items-center h-100">
                    <p class="text-muted">No messages yet. Say hi 👋</p>
                </div>
            @else
                <div class="d-flex justify-content-center align-items-center h-100">
                    <p class="text-muted">Select a teacher on the left to open their chat.</p>
                </div>
            @endif
        </div>

        @if(isset($chat))
            <div class="composer">
                <form id="message-form" action="{{ route('messages.store', $chat->id) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center w-100">
                    @csrf
                    <input type="hidden" name="return_to" value="{{ url()->full() }}">
                    <button type="button" class="btn btn-sm" title="Emoji">😊</button>
                    <input name="content" type="text" class="form-control" placeholder="Message the teacher..." autocomplete="off" required>
                    <label class="mb-0">
                        <input type="file" name="attachment" style="display:none;">
                        <button type="button" class="btn btn-sm" title="Attach">📎</button>
                    </label>
                    <button type="submit" class="btn-send">Send</button>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
(function () {
    const box = document.getElementById('chat-box');
    if (box) setTimeout(()=>{ box.scrollTop = box.scrollHeight; }, 80);

    const search = document.getElementById('teacher-search');
    if (search) {
        search.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            document.querySelectorAll('.chat-users .chat-user').forEach(item => {
                const name = (item.dataset.name || '').toLowerCase();
                item.style.display = name.includes(q) ? '' : 'none';
            });
        });
    }
    const contentInput = document.querySelector('.composer input[name="content"]');
    if (contentInput) contentInput.focus();
})();
</script>
@endsection
