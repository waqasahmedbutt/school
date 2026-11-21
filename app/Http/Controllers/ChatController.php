<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    // List chats for logged-in user
    public function index(Request $request)
    {
        $user = $request->user();

        $chats = Chat::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->with([
                'users:id,name,role',
                'messages' => fn($q) => $q->latest()->limit(1)
            ])
            ->orderByDesc('updated_at')
            ->get();

        return view('chats.index', compact('chats'));
    }

    // Open or create private chat between auth user and another user inside classroom
    public function open(Classroom $classroom, User $otherUser, Request $request)
    {
        $me = $request->user();

        // === Access Control ===
        $isTeacherOfClass = $me->isTeacher()
            && $me->teacherAssignments()->where('classroom_id', $classroom->id)->exists();

        $meIsStudentInClass = $classroom->students()->where('users.id', $me->id)->exists();

        $meIsParentWithChild = $me->isParent()
            && $me->children()->whereHas('classrooms', fn($q) => $q->where('classroom_id', $classroom->id))->exists();

        if (!($isTeacherOfClass || $meIsStudentInClass || $meIsParentWithChild || $me->isAdmin())) {
            abort(403, 'You are not allowed to chat in this classroom.');
        }

        // === Find existing chat ===
        $chat = Chat::where('type', 'private')
            ->where('classroom_id', $classroom->id)
            ->whereHas('participants', fn($q) => $q->where('user_id', $me->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $otherUser->id))
            ->first();

        // === Create if not exists ===
        DB::beginTransaction();
        try {
            if (! $chat) {
                $chat = Chat::create([
                    'classroom_id' => $classroom->id,
                    'created_by'   => $me->id,
                    'type'         => 'private',
                    'subject'      => null,
                ]);

                $chat->participants()->createMany([
                    ['user_id' => $me->id, 'role' => $me->role],
                    ['user_id' => $otherUser->id, 'role' => $otherUser->role],
                ]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('chats.show', $chat->id);
    }

public function listTeachersForParent(Classroom $classroom, Request $request, $sectionId = null)
{
    $me = $request->user();

    // Access check: only parent with child in this classroom (or admin)
    $isParentWithChild = $me->isParent()
        && $me->children()->whereHas('classrooms', fn($q) => $q->where('classroom_id', $classroom->id))->exists();

    if (!($isParentWithChild || $me->isAdmin())) {
        abort(403, 'You are not allowed to view teachers for this classroom.');
    }

    // Parent's children in this classroom
    $children = $me->children()
        ->whereHas('classrooms', fn($q) => $q->where('classroom_id', $classroom->id))
        ->select('users.id', 'users.name')
        ->get();

    // Teachers + subjects (via teacherAssignments)
    $teachers = User::whereHas('teacherAssignments', fn($q) => $q->where('classroom_id', $classroom->id))
        ->with([
            'teacherAssignments' => fn($q) =>
                $q->where('classroom_id', $classroom->id)
                  ->with('subject:id,name')
        ])
        ->select('users.id', 'users.name')
        ->get();

    // Open chat if ?user=ID is passed
    $teacherId = $request->query('user');
    $activeChat = null;
    $messages = collect();
    $activeTeacher = null;

    if ($teacherId) {
        $teacher = User::find($teacherId);

        if ($teacher) {
            $activeTeacher = $teacher;

            $chat = Chat::where('type', 'private')
                ->where('classroom_id', $classroom->id)
                ->whereHas('participants', fn($q) => $q->where('user_id', $me->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $teacher->id))
                ->first();

            if (! $chat) {
                DB::beginTransaction();
                try {
                    $chat = Chat::create([
                        'classroom_id' => $classroom->id,
                        'created_by'   => $me->id,
                        'type'         => 'private',
                        'subject'      => null,
                    ]);

                    $chat->participants()->createMany([
                        ['user_id' => $me->id, 'role' => $me->role],
                        ['user_id' => $teacher->id, 'role' => $teacher->role],
                    ]);

                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            $messages = $chat->messages()
                ->with('sender:id,name')
                ->orderBy('created_at', 'asc')
                ->get();

            $activeChat = $chat;
        }
    }

    return view('parent.child.chat', [
        'classroom'     => $classroom,
        'children'      => $children,
        'teachers'      => $teachers,
        'sectionId'     => $sectionId,
        'activeTeacher' => $activeTeacher,
        'chat'          => $activeChat,
        'messages'      => $messages,
    ]);
}



public function poll(Request $request, Chat $chat)
{
    $after = intval($request->query('after_id', 0));

    // eager load sender to include name in response
    $messages = $chat->messages()
                     ->where('id', '>', $after)
                     ->orderBy('id')
                     ->with('sender:id,name')
                     ->get();

    return response()->json([
        'messages' => $messages->map(function($m) {
            return [
                'id' => $m->id,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender->name ?? null,
                'content' => $m->content,
                'attachment' => $m->attachment,
                'created_at' => $m->created_at->toDateTimeString(),
            ];
        }),
    ]);
}


public function listTeachers(Classroom $classroom, Request $request, $sectionId = null)
{
    $me = $request->user();

    // Access control: student or parent or teacher (so a student can view)
    $isStudentInClass = $classroom->students()->where('users.id', $me->id)->exists();
    $isParentWithChild = $me->isParent() && $me->children()->whereHas('classrooms', fn($q) => $q->where('classroom_id', $classroom->id))->exists();

    if (!($isStudentInClass || $isParentWithChild || $me->isAdmin())) {
        abort(403, 'You are not a participant of this classroom.');
    }

    // Load teachers for this classroom (adjust if you keep teachers differently)
    $teachers = User::whereHas('teacherAssignments', fn($q) => $q->where('classroom_id', $classroom->id))
        ->select('users.id', 'users.name')
        ->get();

    // Optional: add pivot-style metadata if needed (not typical for teacher relation)
    // $teachers->each(fn($t) => $t->role = 'teacher');

    // If ?user=ID passed, open/create private chat and load messages
    $otherUserId = $request->query('user');
    $activeChat = null;
    $messages = collect();
    $activeTeacher = null;

    if ($otherUserId) {
        $otherUser = User::find($otherUserId);

        if ($otherUser) {
            $activeTeacher = $otherUser;

            // find existing private chat between me and teacher in this classroom
            $chat = Chat::where('type', 'private')
                ->where('classroom_id', $classroom->id)
                ->whereHas('participants', fn($q) => $q->where('user_id', $me->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $otherUser->id))
                ->first();

            if (! $chat) {
                DB::beginTransaction();
                try {
                    $chat = Chat::create([
                        'classroom_id' => $classroom->id,
                        'created_by'   => $me->id,
                        'type'         => 'private',
                        'subject'      => null,
                    ]);

                    $chat->participants()->createMany([
                        ['user_id' => $me->id, 'role' => $me->role],
                        ['user_id' => $otherUser->id, 'role' => $otherUser->role],
                    ]);

                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            $messages = $chat->messages()
                ->with('sender:id,name')
                ->orderBy('created_at', 'asc')
                ->get();

            $activeChat = $chat;
        }
    }

    return view('chats.teachers', [
        'classroom'     => $classroom,
        'teachers'      => $teachers,
        'sectionId'     => $sectionId,
        'activeTeacher' => $activeTeacher,
        'chat'          => $activeChat,
        'messages'      => $messages,
    ]);
}


    // Show chat with all messages
 public function show($chatId)
{
    $chat = Chat::with(['users', 'messages.sender']) // eager load sender
        ->findOrFail($chatId);

    $messages = $chat->messages()->orderBy('created_at', 'asc')->get();

    return view('chats.show', compact('chat', 'messages'));
}
// Show list of students in a classroom (optionally by section)
// use statements at top if not present

public function listStudents(Classroom $classroom, Request $request, $sectionId = null)
{
    $user = $request->user();

    // permission: teacher of this class or admin
    if (!($user->isTeacher() && $user->teacherAssignments()->where('classroom_id', $classroom->id)->exists()) 
        && !$user->isAdmin()) {
        abort(403, 'Only teachers can access this.');
    }

    // load students (avoid ambiguous id by prefixing users.*)
    $studentsQuery = $classroom->students()
        ->select('users.id', 'users.name')
        ->withPivot(['section_id', 'roll_no']);

    if ($sectionId) {
        // filter by pivot.section_id
        $studentsQuery->wherePivot('section_id', $sectionId);
    }

    $students = $studentsQuery->get();

    // If teacher clicked a student in sidebar, we'll open/create chat and load messages
    $otherUserId = $request->query('user'); // e.g. ?user=5
    $activeChat = null;
    $messages = collect();
    $activeStudent = null;

    if ($otherUserId) {
        $otherUser = User::find($otherUserId);
        if ($otherUser) {
            $activeStudent = $otherUser;

            // find existing private chat between $user and $otherUser in this classroom
            $chat = Chat::where('type', 'private')
                ->where('classroom_id', $classroom->id)
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $otherUser->id))
                ->first();

            // create if not exists (same logic as your open() but without redirect)
            if (! $chat) {
                DB::beginTransaction();
                try {
                    $chat = Chat::create([
                        'classroom_id' => $classroom->id,
                        'created_by'   => $user->id,
                        'type'         => 'private',
                        'subject'      => null,
                    ]);

                    $chat->participants()->createMany([
                        ['user_id' => $user->id, 'role' => $user->role],
                        ['user_id' => $otherUser->id, 'role' => $otherUser->role],
                    ]);

                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            // load messages for right pane
            $messages = $chat->messages()
                ->with('sender:id,name')
                ->orderBy('created_at', 'asc')
                ->get();

            $activeChat = $chat;
        }
    }

    // pass sectionId so links can preserve it
    return view('chats.students', [
        'classroom'     => $classroom,
        'students'      => $students,
        'sectionId'     => $sectionId,
        'activeStudent' => $activeStudent,
        'chat'          => $activeChat,
        'messages'      => $messages,
    ]);
}


// Show list of parents in a classroom (optionally by section)
public function listParents(Classroom $classroom, Request $request, $sectionId = null)
{
    $user = $request->user();

    // ✅ Authorization check (only teacher of this classroom or admin)
    if (
        !($user->isTeacher() && $user->teacherAssignments()->where('classroom_id', $classroom->id)->exists()) 
        && !$user->isAdmin()
    ) {
        abort(403, 'Only teachers can access this.');
    }

    // ✅ Fetch parents + their children in this classroom/section
    $parents = User::whereHas('children.classrooms', function($q) use ($classroom, $sectionId) {
            $q->where('classroom_id', $classroom->id);
            if ($sectionId) {
                $q->where('classroom_student.section_id', $sectionId);
            }
        })
        ->with(['children' => function($q) use ($classroom, $sectionId) {
            $q->whereHas('classrooms', function($q2) use ($classroom, $sectionId) {
                $q2->where('classroom_id', $classroom->id);
                if ($sectionId) {
                    $q2->where('classroom_student.section_id', $sectionId);
                }
            });
        }])
        ->select('users.id','users.name')
        ->get();
$otherUserId = $request->query('user');
$activeParent = null;
$messages = collect();
$activeChat = null;

if ($otherUserId) {
    $otherUser = User::find($otherUserId);
    if ($otherUser) {
        $activeParent = $otherUser;

        $chat = Chat::where('type', 'private')
            ->where('classroom_id', $classroom->id)
            ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $otherUser->id))
            ->first();

        if (!$chat) {
            DB::beginTransaction();
            try {
                $chat = Chat::create([
                    'classroom_id' => $classroom->id,
                    'created_by'   => $user->id,
                    'type'         => 'private',
                ]);

                $chat->participants()->createMany([
                    ['user_id' => $user->id, 'role' => $user->role],
                    ['user_id' => $otherUser->id, 'role' => $otherUser->role],
                ]);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        }

        $messages = $chat->messages()
            ->with('sender:id,name')
            ->orderBy('created_at')
            ->get();

        $activeChat = $chat;
    }
}

return view('chats.parents', [
    'classroom'    => $classroom,
    'parents'      => $parents,
    'sectionId'    => $sectionId,
    'activeParent' => $activeParent,
    'chat'         => $activeChat,
    'messages'     => $messages,
]);

}
public function listChildren(Request $request)
{
    $parent = $request->user();
    abort_unless($parent->isParent(), 403);

    // Load children with classrooms
    $children = $parent->children()
        ->with('classrooms:id,name') // only basic classroom info
        ->get(['users.id','users.name']);

    return view('chats.parent_children', compact('children'));
}


public function listTeacher($childId, Classroom $classroom, Request $request)
{
    $user = $request->user();

    // ✅ Ensure only parents or admins can access
    if (!$user->isParent() && !$user->isAdmin()) {
        abort(403, 'Only parents can access this.');
    }
    $child = User::find($childId);

    // ✅ Fetch all teachers assigned to this classroom
    $teachers = User::whereHas('teacherAssignments', function($q) use ($classroom) {
            $q->where('classroom_id', $classroom->id);
        })
        ->select('id', 'name')
        ->get();

    $teacherId = $request->query('teacher');
$activeTeacher = null;
$messages = collect();
$activeChat = null;

if ($teacherId) {
    $otherUser = User::find($teacherId);

    if ($otherUser) {
        $activeTeacher = $otherUser;

        $chat = Chat::where('type', 'private')
            ->where('classroom_id', $classroom->id)
            ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $otherUser->id))
            ->first();

        if (!$chat) {
            DB::beginTransaction();
            try {
                $chat = Chat::create([
                    'classroom_id' => $classroom->id,
                    'created_by'   => $user->id,
                    'type'         => 'private',
                ]);

                $chat->participants()->createMany([
                    ['user_id' => $user->id, 'role' => $user->role],
                    ['user_id' => $otherUser->id, 'role' => $otherUser->role],
                ]);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        }

        $messages = $chat->messages()
            ->with('sender:id,name')
            ->orderBy('created_at')
            ->get();

        $activeChat = $chat;
    }
}


    return view('chats.parent_teachers', [
        'classroom'     => $classroom,
        'teachers'      => $teachers,
        'child'       => $child,
        'activeTeacher' => $activeTeacher,
        'chat'          => $activeChat,
        'messages'      => $messages,
    ]);
}

}