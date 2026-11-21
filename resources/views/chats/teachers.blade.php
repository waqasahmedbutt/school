@extends('layouts.app')

@section('content')
<div class="chat-container">
    <div class="chat-wrapper">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <div class="user-info">
                    <div class="avatar teacher-avatar">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <div class="name">Teacher Portal</div>
                        <div class="status">Chat with Teachers</div>
                    </div>
                </div>
            </div>

            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="teacher-search" placeholder="Search teachers...">
                </div>
            </div>

            <div class="section-info">
                <span>Teachers ({{ count($teachers) }})</span>
                @if($sectionId)
                    <span class="section-name">Section: {{ $sectionId }}</span>
                @endif
            </div>

            <div class="chat-users">
                @forelse($teachers as $teacher)
                    @php
                        $isActive = isset($activeTeacher) && $activeTeacher && $activeTeacher->id === $teacher->id;
                        $link = route('chats.teachers', [$classroom->id, $sectionId ?? null]) . '?user=' . $teacher->id;
                        $avatarClass = 'avatar-' . (($loop->index % 5) + 1);
                    @endphp

                    <a href="{{ $link }}" class="chat-user {{ $isActive ? 'active' : '' }}" data-name="{{ strtolower($teacher->name) }}">
                        <div class="avatar {{ $avatarClass }}">{{ strtoupper(substr($teacher->name,0,1)) }}</div>
                        <div class="user-info">
                            <div class="name">{{ $teacher->name }}</div>
                            <div class="details">Teacher • Click to open chat</div>
                        </div>
                        @if(!empty($teacher->unread_count) && $teacher->unread_count > 0)
                            <span class="unread-badge">{{ $teacher->unread_count }}</span>
                        @endif
                    </a>
                @empty
                    <div class="empty-state">No teachers found.</div>
                @endforelse
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main">
            @if(isset($activeTeacher))
                <div class="chat-header">
                    <div class="chat-partner">
                        <div class="avatar {{ 'avatar-' . (array_search($activeTeacher->id, array_keys($teachers->keyBy('id')->toArray())) % 5 + 1) }}">
                            {{ strtoupper(substr($activeTeacher->name,0,1)) }}
                        </div>
                        <div class="partner-info">
                            <div class="name">{{ $activeTeacher->name }}</div>
                            <div class="status">
                                <span class="online-dot"></span> Online • Teacher
                            </div>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <a href="{{ route('chats.index') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <button class="btn-action">
                            <i class="fas fa-phone"></i>
                        </button>
                        <button class="btn-action">
                            <i class="fas fa-video"></i>
                        </button>
                        <button class="btn-action">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>

                <div class="messages-container" id="chat-box">
                    @if(isset($chat) && $messages->isNotEmpty())
                        @foreach($messages as $msg)
                            @php $isMe = $msg->sender_id === auth()->id(); @endphp
                            <div class="message-row {{ $isMe ? 'outgoing' : 'incoming' }}">
                                <div class="message {{ $isMe ? 'sent' : 'received' }}">
                                    @if(!$isMe)
                                        <div class="sender-name">{{ $msg->sender->name }}</div>
                                    @endif
                                    <div class="message-content">{{ $msg->content }}</div>
                                    @if($msg->attachment)
                                        <div class="attachment">
                                            <a href="{{ asset('storage/' . $msg->attachment) }}" target="_blank">
                                                <i class="fas fa-paperclip"></i> View Attachment
                                            </a>
                                        </div>
                                    @endif
                                    <div class="message-time">{{ $msg->created_at->format('h:i A') }}</div>
                                </div>
                            </div>
                        @endforeach
                    @elseif(isset($chat))
                        <div class="empty-chat">
                            <i class="fas fa-comments"></i>
                            <p>No messages yet. Say hello to start the conversation!</p>
                        </div>
                    @endif
                </div>

                <div class="message-composer">
                    <button class="composer-action">
                        <i class="fas fa-smile"></i>
                    </button>
                    <button class="composer-action">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <form id="message-form" action="{{ route('messages.store', $chat->id) }}" method="POST" enctype="multipart/form-data" class="message-form">
                        @csrf
                        <input type="hidden" name="return_to" value="{{ url()->full() }}">
                        <input type="text" name="content" placeholder="Type a message..." autocomplete="off">
                    </form>
                    <button type="submit" form="message-form" class="send-button">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            @else
                <div class="no-chat-selected">
                    <div class="no-chat-icon">
                        <i class="fas fa-comment-alt"></i>
                    </div>
                    <h3>Select a teacher to start chatting</h3>
                    <p>Choose a teacher from the list to view your conversation</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    :root {
        --primary-color: #25D366;
        --primary-dark: #128C7E;
        --secondary-color: #34B7F1;
        --light-bg: #f0f2f5;
        --message-sent: #DCF8C6;
        --message-received: #FFFFFF;
        --text-primary: #3B4A54;
        --text-secondary: #667781;
        --border-color: #E6E6E6;
        --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .chat-container {
        height: 100vh;
        background-color: #f5f5f5;
        font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
    }

    .chat-wrapper {
        display: flex;
        height: 100%;
        max-width: 1600px;
        margin: 0 auto;
        background: white;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    /* Sidebar Styles */
    .chat-sidebar {
        width: 380px;
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        background: white;
    }

    .sidebar-header {
        padding: 16px 20px;
        background: var(--light-bg);
        border-bottom: 1px solid var(--border-color);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: white;
        font-size: 18px;
    }

    .teacher-avatar {
        background: linear-gradient(135deg, #6a6df1, #4a4fcf);
    }

    .avatar-1 { background: linear-gradient(135deg, #6dd3b1, #2abf91); }
    .avatar-2 { background: linear-gradient(135deg, #8ec5ff, #6a9fff); }
    .avatar-3 { background: linear-gradient(135deg, #ffd6a5, #ff8c66); }
    .avatar-4 { background: linear-gradient(135deg, #d9a0f9, #b66dff); }
    .avatar-5 { background: linear-gradient(135deg, #ff9b9b, #ff6b6b); }

    .sidebar-header .name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .sidebar-header .status {
        color: var(--text-secondary);
        font-size: 13px;
    }

    .search-container {
        padding: 12px 15px;
        background: white;
        border-bottom: 1px solid var(--border-color);
    }

    .search-box {
        background: var(--light-bg);
        border-radius: 20px;
        padding: 8px 16px;
        display: flex;
        align-items: center;
    }

    .search-box i {
        color: var(--text-secondary);
        margin-right: 8px;
    }

    .search-box input {
        background: transparent;
        border: none;
        width: 100%;
        padding: 6px 10px;
        outline: none;
    }

    .section-info {
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        color: var(--text-secondary);
        background: #fafafa;
        border-bottom: 1px solid var(--border-color);
    }

    .section-name {
        font-weight: 500;
        color: var(--primary-dark);
    }

    .chat-users {
        overflow-y: auto;
        flex: 1;
    }

    .chat-user {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 16px;
        cursor: pointer;
        transition: background 0.2s;
        border-bottom: 1px solid #f5f5f5;
        text-decoration: none;
        color: inherit;
    }

    .chat-user:hover {
        background: #f9f9f9;
    }

    .chat-user.active {
        background: #e9f5fe;
        border-left: 4px solid var(--secondary-color);
    }

    .user-info .name {
        font-weight: 600;
        margin-bottom: 4px;
        color: var(--text-primary);
    }

    .user-info .details {
        color: var(--text-secondary);
        font-size: 13px;
    }

    .unread-badge {
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
    }

    .empty-state {
        padding: 30px 20px;
        text-align: center;
        color: var(--text-secondary);
    }

    /* Main Chat Area */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: var(--light-bg);
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23e6e6e6' fill-opacity='0.3' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .chat-header {
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: white;
        border-bottom: 1px solid var(--border-color);
    }

    .chat-partner {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .partner-info .name {
        font-weight: 600;
        font-size: 16px;
    }

    .partner-info .status {
        color: var(--text-secondary);
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .online-dot {
        width: 10px;
        height: 10px;
        background: var(--primary-color);
        border-radius: 50%;
        display: inline-block;
    }

    .chat-actions {
        display: flex;
        gap: 15px;
    }

    .btn-back, .btn-action {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 18px;
        cursor: pointer;
        padding: 5px;
    }

    .btn-back:hover, .btn-action:hover {
        color: var(--secondary-color);
    }

    .messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }

    .message-row {
        display: flex;
        margin-bottom: 12px;
    }

    .message-row.outgoing {
        justify-content: flex-end;
    }

    .message-row.incoming {
        justify-content: flex-start;
    }

    .message {
        max-width: 65%;
        padding: 10px 14px;
        border-radius: 8px;
        position: relative;
        word-wrap: break-word;
        line-height: 1.4;
    }

    .message.sent {
        background: var(--message-sent);
        border-top-right-radius: 2px;
    }

    .message.received {
        background: var(--message-received);
        border-top-left-radius: 2px;
        box-shadow: var(--shadow);
    }

    .sender-name {
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 4px;
        color: var(--primary-dark);
    }

    .message-content {
        margin-bottom: 5px;
    }

    .attachment a {
        color: var(--secondary-color);
        text-decoration: none;
        font-size: 13px;
    }

    .attachment a:hover {
        text-decoration: underline;
    }

    .message-time {
        text-align: right;
        font-size: 11px;
        color: var(--text-secondary);
        margin-top: 4px;
    }

    .empty-chat {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        color: var(--text-secondary);
    }

    .empty-chat i {
        font-size: 60px;
        margin-bottom: 20px;
        color: #d9d9d9;
    }

    .message-composer {
        padding: 12px 20px;
        background: white;
        display: flex;
        align-items: center;
        gap: 12px;
        border-top: 1px solid var(--border-color);
    }

    .composer-action {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 18px;
        cursor: pointer;
        padding: 5px;
    }

    .composer-action:hover {
        color: var(--secondary-color);
    }

    .message-form {
        flex: 1;
    }

    .message-form input {
        width: 100%;
        border: none;
        border-radius: 20px;
        padding: 12px 18px;
        background: var(--light-bg);
        outline: none;
    }

    .send-button {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .send-button:hover {
        background: var(--primary-dark);
    }

    .no-chat-selected {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        color: var(--text-secondary);
    }

    .no-chat-icon {
        font-size: 60px;
        margin-bottom: 20px;
        color: #d9d9d9;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .chat-sidebar {
            width: 320px;
        }
    }

    @media (max-width: 768px) {
        .chat-wrapper {
            flex-direction: column;
        }
        
        .chat-sidebar {
            width: 100%;
            height: 40%;
            border-right: none;
            border-bottom: 1px solid var(--border-color);
        }
        
        .chat-main {
            height: 60%;
        }
    }

    /* Custom scrollbar */
    .chat-users::-webkit-scrollbar,
    .messages-container::-webkit-scrollbar {
        width: 6px;
    }

    .chat-users::-webkit-scrollbar-track,
    .messages-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .chat-users::-webkit-scrollbar-thumb,
    .messages-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .chat-users::-webkit-scrollbar-thumb:hover,
    .messages-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const chatBox = document.getElementById('chat-box');
  const input = document.querySelector('input[name="content"], textarea[name="content"]');
  const sendBtn = document.getElementById('send-button') || document.querySelector('.send-button');
  const chatId = @json($chat->id ?? null);

  if (!chatBox || !chatId) {
    console.warn('Chat poller: no chatBox or chatId found — poller disabled.');
    return;
  }

  // Determine last message id from DOM (ignore temp-* ids)
  let lastMessageId = (function () {
    const ids = Array.from(chatBox.querySelectorAll('[data-message-id]'))
      .map(el => el.dataset.messageId)
      .filter(id => id && !id.startsWith('temp-'))
      .map(id => Number(id))
      .filter(n => !isNaN(n));
    return ids.length ? Math.max(...ids) : null;
  })();

  function isNearBottom(threshold = 120) {
    return (chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight) <= threshold;
  }
  function scrollToBottom(smooth = true) {
    if (smooth) chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
    else chatBox.scrollTop = chatBox.scrollHeight;
  }

  function createMessageNode(msg, currentUserId) {
    const row = document.createElement('div');
    row.className = msg.sender_id === currentUserId ? 'message-row outgoing' : 'message-row incoming';
    row.dataset.messageId = msg.id;

    const inner = document.createElement('div');
    inner.className = msg.sender_id === currentUserId ? 'message sent' : 'message received';

    let html = '';
    if (msg.sender_id !== currentUserId && msg.sender && msg.sender.name) {
      html += `<div class="sender-name">${escapeHtml(msg.sender.name)}</div>`;
    }
    html += `<div class="message-content">${escapeHtml(msg.content || '')}</div>`;
    if (msg.attachment) {
      // server is expected to return a full URL for attachments (Storage::url)
      html += `<div class="attachment"><a href="${escapeHtml(msg.attachment)}" target="_blank"><i class="fas fa-paperclip"></i> View Attachment</a></div>`;
    }
    html += `<div class="message-time">${new Date(msg.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}</div>`;

    inner.innerHTML = html;
    row.appendChild(inner);
    return row;
  }

  function escapeHtml(s) {
    return String(s || '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  // Polling function
  async function fetchNewMessages() {
    try {
      // don't poll when tab hidden (save resources)
      if (document.hidden) return;
      // don't poll while user typing in input or while sending
      if (input && document.activeElement === input) return;
      if (sendBtn && sendBtn.classList.contains('sending')) return;

      const url = lastMessageId ? `/messages/latest/${chatId}?after_id=${lastMessageId}` : `/messages/latest/${chatId}`;
      const resp = await fetch(url, {
        method: 'GET',
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      });

      if (resp.redirected) {
        // session/login redirect happened
        console.warn('Message poll redirected (likely auth).');
        return;
      }
      if (!resp.ok) {
        // ignore non-OK silently (you can log for debugging)
        console.debug('Message poll returned non-ok', resp.status);
        return;
      }

      const json = await resp.json().catch(() => null);
      if (!json) return;
      const messages = json.messages || [];

      if (!messages.length) return;

      // append only new messages not already present
      const atBottom = isNearBottom();
      const frag = document.createDocumentFragment();
      const currentUserId = @json(auth()->id());

      messages.forEach(msg => {
        // skip if already in DOM
        if (chatBox.querySelector(`[data-message-id="${msg.id}"]`)) return;
        const node = createMessageNode(msg, currentUserId);
        frag.appendChild(node);
        // update lastMessageId
        const n = Number(msg.id);
        if (!isNaN(n)) lastMessageId = lastMessageId ? Math.max(lastMessageId, n) : n;
      });

      if (frag.childNodes.length) {
        chatBox.appendChild(frag);
        // only auto-scroll if user was at bottom before new messages
        if (atBottom) scrollToBottom();
      }

    } catch (err) {
      console.error('Failed to fetch new messages', err);
    }
  }

  // start polling every 2 seconds
  fetchNewMessages(); // initial fetch
  const timer = setInterval(fetchNewMessages, 2000);

  // always keep chat at end on initial load
  if (isNearBottom()) scrollToBottom(false);

  // cleanup when leaving page
  window.addEventListener('beforeunload', () => clearInterval(timer));
});
</script>


@endsection