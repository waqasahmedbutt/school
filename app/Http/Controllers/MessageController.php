<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
public function store(Request $request, Chat $chat)
{
    $request->validate([
        'content' => 'nullable|string',
        'attachment' => 'nullable|file|max:2048',
    ]);

    $data = [
        'sender_id' => $request->user()->id,
        'content' => $request->input('content'),
    ];

    // Handle attachment
    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $path = $file->store('chat_attachments', 'public');
        $data['attachment'] = $path;
    }

    $message = $chat->messages()->create($data);

    // Load sender relation for JSON response
    $message->load('sender');

    // Prepare JSON-friendly payload
    $payload = [
        'id' => $message->id,
        'sender_id' => $message->sender_id,
        'sender' => $message->sender ? [
            'id' => $message->sender->id,
            'name' => $message->sender->name,
        ] : null,
        'content' => $message->content,
        'attachment' => $message->attachment ? Storage::url($message->attachment) : null,
        'created_at' => $message->created_at->toDateTimeString(),
    ];

    // Return JSON for AJAX/fetch requests, otherwise redirect
    if ($request->wantsJson() || $request->expectsJson() || $request->ajax()) {
        return response()->json([
            'status' => 'success',
            'message' => $payload,
        ], 201);
    }

    $returnTo = $request->input('return_to', route('chats.show', $chat->id));
    return redirect($returnTo)->with('success', 'Message sent.');
}


}
