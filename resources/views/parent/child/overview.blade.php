@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">{{ $child->name }} — Overview</h1>
        <div>
            <a href="{{ route('parent.child.attendance', $child->id) }}" class="mr-2 underline">Attendance</a>
            <a href="{{ route('parent.child.assignments', $child->id) }}" class="mr-2 underline">Assignments</a>
            <a href="{{ route('parent.child.quizzes', $child->id) }}" class="mr-2 underline">Quizzes</a>
            <a href="{{ route('parent.child.physical_exams', $child->id) }}" class="mr-2 underline">Classroom Tests</a>
            <a href="{{ route('parent.child.exams', $child->id) }}" class="underline">Exams</a>
        </div>
    </div>

    {{-- Top summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 border rounded">
            <div class="text-sm text-gray-500">Assignments</div>
            <div class="text-2xl font-bold">{{ $summary['assignments']['total'] }}</div>
            <div class="text-sm text-gray-600">Submitted: {{ $summary['assignments']['submitted'] }} • Avg: {{ $summary['assignments']['average_marks'] ?? '–' }}</div>
        </div>

        <div class="p-4 border rounded">
            <div class="text-sm text-gray-500">Quizzes</div>
            <div class="text-2xl font-bold">{{ $summary['quizzes']['attempts'] }}</div>
            <div class="text-sm text-gray-600">Avg Score: {{ $summary['quizzes']['average_score'] ?? '–' }}</div>
        </div>

        <div class="p-4 border rounded">
            <div class="text-sm text-gray-500">Attendance (30d)</div>
            <div class="text-2xl font-bold">{{ $summary['attendance']['percent_last_30_days'] }}%</div>
            <div class="text-sm text-gray-600">Present: {{ $summary['attendance']['present_last_30'] }} / {{ $summary['attendance']['total_days_last_30'] }}</div>
        </div>
    </div>

    {{-- Per-classroom / section breakdown --}}
    <div class="mt-6 space-y-6">
        @foreach($classroomDetails as $detail)
            @php
                $classroom = $detail['classroom'];
                $section = $detail['section'];
                $rollNo = $detail['roll_no'];
            @endphp

            <div class="border rounded p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-lg font-semibold">{{ $classroom->name ?? 'Classroom '.$classroom->id }}</div>
                        <div class="text-sm text-gray-600">
                            Section: {{ $section->name ?? '–' }} • Roll No: {{ $rollNo ?? '–' }}
                        </div>
                    </div>
                    <div class="text-sm">
                        <a href="{{ route('parent.child.assignments', $child->id) }}?classroom={{ $classroom->id }}" class="underline mr-2">View all assignments</a>
                        <a href="{{ route('parent.child.quizzes', $child->id) }}?classroom={{ $classroom->id }}" class="underline">View all quizzes</a>
                    </div>
                </div>

                {{-- subjects + teachers --}}
                <div class="mt-3">
                    <div class="text-sm text-gray-500 mb-2">Subjects & Teachers</div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        @forelse($detail['subjects'] as $ta)
                            <div class="p-2 border rounded">
                                <div class="font-medium">{{ $ta->subject->name ?? 'Subject' }}</div>
                                <div class="text-xs text-gray-600">{{ $ta->teacher->name ?? 'Teacher' }}</div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">No subjects assigned for this section.</div>
                        @endforelse
                    </div>
                </div>

                {{-- assignments for this section (show a few) --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">Assignments (section)</div>
                        <div class="text-xs text-gray-500">{{ $detail['assignments']->count() }} total</div>
                    </div>

                    <ul class="mt-2">
                        @forelse($detail['assignments']->take(5) as $a)
                            @php $sub = $detail['assignment_submissions'][$a->id] ?? null; @endphp
                            <li class="py-2 border-b">
                                <div class="font-medium">{{ $a->title }}</div>
                                <div class="text-xs text-gray-500">
                                    Subject: {{ $a->subject->name ?? '–' }} • Due: {{ optional($a->due_at)->format('Y-m-d') ?? '–' }}
                                    • Status: {{ $sub ? 'Submitted' : 'Not Submitted' }}
                                    • Marks: {{ $sub->marks_awarded ?? '–' }}
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-gray-500">No assignments for this section</li>
                        @endforelse
                    </ul>
                </div>

                {{-- quizzes for this section (show a few) --}}
                <div class="mt-4">
                    <div class="text-sm text-gray-500">Quizzes (section)</div>
                    <ul class="mt-2">
                        @forelse($detail['quizzes']->take(5) as $q)
                            @php $attempt = $detail['quiz_attempts'][$q->id] ?? null; @endphp
                            <li class="py-2 border-b">
                                <div class="font-medium">{{ $q->title }}</div>
                                <div class="text-xs text-gray-500">
                                    Subject: {{ $q->subject->name ?? '–' }} • Date: {{ optional($q->created_at)->format('Y-m-d') ?? '–' }}
                                    • Status: {{ $attempt ? 'Attempted' : 'Not Taken' }}
                                    • Score: {{ $attempt->score ?? '–' }}
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-gray-500">No quizzes for this section</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Recent assignments & quiz attempts (global across student's sections) --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 border rounded">
            <h3 class="font-semibold mb-2">Recent Assignments (relevant to student's section(s))</h3>
            <ul>
                @forelse($recentAssignments as $a)
                    <li class="py-2 border-b">
                        <div class="font-medium">{{ $a->title }}</div>
                        <div class="text-xs text-gray-500">Subject: {{ $a->subject->name ?? '–' }} • Due: {{ optional($a->due_at)->format('Y-m-d') ?? '–' }}</div>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">No recent assignments</li>
                @endforelse
            </ul>
        </div>

        <div class="p-4 border rounded">
            <h3 class="font-semibold mb-2">Recent Quiz Attempts</h3>
            <ul>
                @forelse($recentQuizAttempts as $q)
                    <li class="py-2 border-b">
                        <div class="font-medium">{{ $q->quiz->title ?? 'Quiz' }} — Score: {{ $q->score ?? '–' }}</div>
                        <div class="text-xs text-gray-500">Subject: {{ $q->quiz->subject->name ?? '–' }} • Finished: {{ optional($q->finished_at)->format('Y-m-d H:i') ?? '–' }}</div>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">No recent attempts</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
