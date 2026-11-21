@extends('layouts.app')
@section('title', $classroom->name . ' — Section ' . ($section->name ?? ''))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-center">
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $classroom->name }}</h1>
                <p class="text-blue-100 mb-4">Section: {{ $section->name ?? '—' }}</p>
                
                {{-- Section selector --}}
                @if(isset($allAssignments) && $allAssignments->isNotEmpty())
                    @php
                        $sections = $allAssignments->pluck('section')->unique('id')->values();
                    @endphp

                    @if($sections->count() > 1)
                        <form method="GET" action="{{ route('teacher.classrooms.show', $classroom) }}" class="mt-3">
                            <label class="text-blue-100 mr-2">Select section:</label>
                            <select name="section_id" onchange="this.form.submit()" class="bg-blue-500 text-white border-0 px-3 py-1.5 rounded-lg">
                                @foreach($sections as $sec)
                                    <option value="{{ $sec->id }}" {{ optional($section)->id == $sec->id ? 'selected' : '' }}>
                                        {{ $sec->name }}
                                    </option>
                                @endforeach
                            </select>
                            <noscript><button class="ml-2 px-3 py-1.5 bg-blue-800 text-white rounded-lg">Go</button></noscript>
                        </form>
                    @endif
                @endif
            </div>
            <div class="mt-4 md:mt-0">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="w-20 h-20 opacity-90">
            </div>
        </div>
    </div>

    {{-- Subject Filter --}}
    <div class="bg-white p-6 rounded-2xl shadow-md mb-8">
        <h2 class="font-semibold text-xl mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Filter by Subject
        </h2>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('teacher.classrooms.show', ['classroom' => $classroom->id, 'section_id' => $section->id]) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 {{ $subjectId ? 'bg-gray-100 text-gray-800' : 'bg-indigo-600 text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
                All Subjects
            </a>

            @foreach($myAssignments->pluck('subject')->unique('id') as $subj)
                <a href="{{ route('teacher.classrooms.show', ['classroom' => $classroom->id, 'section_id' => $section->id, 'subject_id' => $subj->id]) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ $subjectId == $subj->id ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }}">
                    {{ $subj->name }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <a href="{{ route('teacher.assignments.create', $classroom) }}?section_id={{ $section->id }}" class="bg-white p-5 rounded-2xl shadow-md text-center group hover:shadow-lg transition-all">
            <div class="bg-green-100 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="font-medium">New Assignment</h3>
        </a>

        <a href="{{ route('teacher.quizzes.create', $classroom) }}?section_id={{ $section->id }}" class="bg-white p-5 rounded-2xl shadow-md text-center group hover:shadow-lg transition-all">
            <div class="bg-yellow-100 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="font-medium">New Quiz</h3>
        </a>

        <a href="{{ route('teacher.physical_exams.create', $classroom) }}?section_id={{ $section->id }}" class="bg-white p-5 rounded-2xl shadow-md text-center group hover:shadow-lg transition-all">
            <div class="bg-indigo-100 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="font-medium">New Exam</h3>
        </a>

        <a href="{{ route('admin.timetable.export', ['classroom_id' => $classroom->id, 'section_id' => $section->id]) }}" class="bg-white p-5 rounded-2xl shadow-md text-center group hover:shadow-lg transition-all">
            <div class="bg-blue-100 w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            </div>
            <h3 class="font-medium">Export Timetable</h3>
        </a>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="md:col-span-2 space-y-8">
            {{-- Assignments --}}
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-semibold text-xl flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Assignments
                    </h2>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $assignments->count() }} total</span>
                </div>
                
                @if($assignments->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p>No assignments yet for this section.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($assignments as $a)
                            <div class="border border-gray-200 rounded-xl p-5 flex justify-between items-center transition-all hover:shadow-sm">
                                <div class="flex-1">
                                    <div class="font-medium text-lg mb-1">{{ $a->title }}</div>
                                    <div class="text-sm text-gray-600 mb-2">{{ $a->subject->name ?? 'No Subject' }}</div>
                                    <div class="flex flex-wrap gap-3 text-xs">
                                        <span class="bg-gray-100 text-gray-800 px-2.5 py-1 rounded-full flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Due: {{ $a->due_at ? $a->due_at->format('M j, Y H:i') : 'No deadline' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('teacher.assignments.edit', $a) }}" class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium transition-all shadow-sm">Edit</a>
                                    <form action="{{ route('teacher.assignments.destroy', $a) }}" method="POST" onsubmit="return confirm('Delete assignment?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-all shadow-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Quizzes --}}
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-semibold text-xl flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Quizzes
                    </h2>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $quizzes->count() }} total</span>
                </div>
                
                @if($quizzes->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p>No quizzes yet for this section.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($quizzes as $q)
                            <div class="border border-gray-200 rounded-xl p-5 flex justify-between items-center transition-all hover:shadow-sm">
                                <div class="flex-1">
                                    <div class="font-medium text-lg mb-1 flex items-center gap-2">
                                        {{ $q->title }}
                                        @if($q->is_published) 
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Published</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600 mb-2">{{ $q->subject->name ?? 'No Subject' }}</div>
                                    <div class="text-sm text-gray-500 mb-3">{{ \Str::limit($q->description, 120) }}</div>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('teacher.quizzes.edit', $q) }}" class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium transition-all shadow-sm text-center">Edit</a>
                                    
                                    @if(!$q->is_published)
                                        <form action="{{ route('teacher.quizzes.publish', $q) }}" method="POST">
                                            @csrf
                                            <button class="w-full px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all shadow-sm">Publish</button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('teacher.quizzes.destroy', $q) }}" method="POST" onsubmit="return confirm('Delete quiz?')">
                                        @csrf @method('DELETE')
                                        <button class="w-full px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-all shadow-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-8">
            {{-- Students --}}
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Students ({{ $students->count() }})
                </h3>
                
                @if($students->isEmpty())
                    <div class="text-center py-4 text-gray-500">
                        <p>No students in this section.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($students as $s)
                            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-all">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-800 flex items-center justify-center font-medium">
                                    {{ strtoupper(substr($s->student->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium truncate">{{ $s->student->name }}</div>
                                    <div class="text-xs text-gray-500">Roll: {{ $s->roll_no ?? '—' }}</div>
                                </div>
                                <div class="text-xs">
                                    @if(Route::has('teacher.students.show'))
                                        <a href="{{ route('teacher.students.show', $s->student->id) }}" class="text-indigo-600 hover:text-indigo-800">Profile</a>
                                    @else
                                        <span class="text-gray-400">Profile</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Exams --}}
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Exams
                </h3>
                
                @if($exams->isEmpty())
                    <div class="text-center py-4 text-gray-500">
                        <p>No exams created yet for this classroom.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($exams as $exam)
                            <div class="border border-gray-200 rounded-xl p-4">
                                <div class="font-medium mb-2">{{ $exam->title }}</div>
                                <div class="text-xs text-gray-500 mb-3">
                                    Date: {{ $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('M j, Y') : '-' }}
                                    • Total: {{ $exam->total_marks ?? '-' }}
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @forelse($myAssignments as $assign)
                                        @if($assign && $assign->subject_id && $assign->subject)
                                            <a href="{{ route('teacher.exams.enter-marks', [$exam->id, $assign->subject_id]) }}?section_id={{ $section->id }}"
                                               class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition-all">
                                                {{ $assign->subject->name }}
                                            </a>
                                        @endif
                                    @empty
                                        <span class="text-xs text-gray-400">No subjects</span>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Physical Exams --}}
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Classroom Tests
                </h3>
                
                @if($physicalExams->isEmpty())
                    <div class="text-center py-4 text-gray-500">
                        <p>No classroom tests for this section.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($physicalExams as $e)
                            <div class="border border-gray-200 rounded-xl p-4">
                                <div class="font-medium mb-1">{{ $e->title }}</div>
                                <div class="text-xs text-gray-500 mb-3">
                                    Date: {{ $e->exam_date->format('M j, Y') }} • Total: {{ $e->total_marks }}
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('teacher.physical_exams.show', $e) }}" 
                                       class="flex-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-all shadow-sm text-center">
                                        Marks
                                    </a>

                                    <form action="{{ route('exams.destroy', $e) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this exam?');" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-all shadow-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Attendance --}}
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 极 012-2h2a2 2 0 012 2" />
                    </svg>
                    Attendance
                </h3>
                <a href="{{ route('teacher.attendance.form', [$classroom->id, $section->id]) }}" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-center font-medium transition-all shadow-sm">
                    Mark Attendance
                </a>
            </div>

            {{-- Chats --}}
            @if(auth()->user()->isTeacher())
                <div class="bg-white p-6 rounded-2xl shadow-md">
                    <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="极 0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Communication
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-3">
                        {{-- Chat with Students --}}
                        <a href="{{ route('chats.students', [$classroom->id, $section->id]) }}"
                           class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-center font-medium transition-all shadow-sm flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.极 1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 极 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Chat with Students
                        </a>

                        {{-- Chat with Parents --}}
                        <a href="{{ route('chats.parents', [$classroom->id, $section->id]) }}"
                           class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-center font-medium transition-all shadow-sm flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Chat with Parents
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection