@extends('layouts.app')
@section('title', $classroom->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-center">
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $classroom->name }}</h1>
                <p class="text-blue-100 mb-4">Section: {{ optional($classroom->sections->where('id', $sectionId)->first())->name ?? 'Your section' }}</p>
                <p class="text-sm opacity-90 italic">"The beautiful thing about learning is that no one can take it away from you." — B.B. King</p>
            </div>
            <div class="mt-4 md:mt-0">
                <img src="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" class="w-20 h-20 opacity-90">
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="flex flex-wrap gap-3 justify-center md:justify-end mb-8">
        <a href="{{ route('student.assignments.index') }}" class="px-5 py-3 bg-white hover:bg-gray-50 rounded-xl shadow-md text-sm font-medium flex items-center gap-2 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            All Assignments
        </a>
        <a href="{{ route('student.quizzes.index') }}" class="px-5 py-3 bg-white hover:bg-gray-50 rounded-xl shadow-md text-sm font-medium flex items-center gap-2 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            All Quizzes
        </a>
        <a href="{{ route('chats.teachers', [$classroom->id, $sectionId ?? null]) }}" class="px-5 py-3 bg-white hover:bg-gray-50 rounded-xl shadow-md text-sm font-medium flex items-center gap-2 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            Chat with Teachers
        </a>
        <a href="{{ route('admin.timetable.export', ['classroom_id' => $classroom->id, 'section_id' => $sectionId]) }}" class="bg-white p-5 rounded-2xl shadow-md text-center group hover:shadow-lg transition-all">
          
            <h3 class="font-medium">Export Timetable</h3>
        </a>
    </div>

    <div class="grid md:grid-cols-4 gap-8">
        {{-- Main Content --}}
        <div class="md:col-span-3 space-y-8">
            {{-- Subjects & Teachers --}}
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h2 class="font-semibold text-xl mb-5 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                    </svg>
                    Subjects & Teachers
                </h2>
                
                @if($subjects->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p>No subjects assigned yet.</p>
                    </div>
                @else
                    <div class="flex flex-wrap gap-3 mb-4" id="subjects-bar">
                        <button data-subject-id="" class="subject-btn px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium shadow-md transition-all flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                            All Subjects
                        </button>
                        @foreach($subjects as $sa)
                            <button
                                class="subject-btn px-5 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm transition-all"
                                data-subject-id="{{ $sa->subject->id }}"
                                title="{{ $sa->teacher->name ?? '' }} — Section: {{ $sa->section->name ?? '' }}">
                                <div class="font-medium">{{ $sa->subject->name ?? '—' }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $sa->teacher->name ?? '—' }}</div>
                            </button>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500">Click a subject to filter assignments & quizzes.</p>
                @endif
            </div>

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
                
                <div id="assignments-list" class="space-y-4">
                    @forelse($assignments as $a)
                        <div class="border border-gray-200 rounded-xl p-5 flex justify-between items-center assignment-item transition-all hover:shadow-sm"
                                data-subject-id="{{ $a->subject_id }}">
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
                                    <span class="bg-gray-100 text-gray-800 px-2.5 py-1 rounded-full flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        By: {{ $a->creator->name ?? 'Unknown' }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('student.assignments.show', $a) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all shadow-sm">Open</a>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p>No assignments yet. 🌱</p>
                        </div>
                    @endforelse
                </div>
                <div id="assignments-empty" class="hidden text-center py-8 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>No assignments for this subject.</p>
                </div>
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
                
                <div id="quizzes-list" class="space-y-4">
                    @forelse($quizzes as $q)
                        <div class="border border-gray-200 rounded-xl p-5 flex justify-between items-center quiz-item transition-all hover:shadow-sm"
                                data-subject-id="{{ $q->subject_id }}">
                            <div class="flex-1">
                                <div class="font-medium text-lg mb-1">{{ $q->title }}</div>
                                <div class="text-sm text-gray-600 mb-2">{{ $q->subject->name ?? 'No Subject' }}</div>
                                <div class="flex flex-wrap gap-3 text-xs">
                                    <span class="bg-gray-100 text-gray-800 px-2.5 py-1 rounded-full flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Duration: {{ $q->duration_minutes ?? '—' }} mins
                                    </span>
                                    <span class="bg-gray-100 text-gray-800 px-2.5 py-1 rounded-full flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        By: {{ $q->creator->name ?? 'Unknown' }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('student.quizzes.show', $q) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium transition-all shadow-sm">Open</a>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p>No quizzes yet. ✨</p>
                        </div>
                    @endforelse
                </div>
                <div id="quizzes-empty" class="hidden text-center py-8 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>No quizzes for this subject.</p>
                </div>
            </div>

            {{-- Exams & Results --}}
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-semibold text-xl flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Exams & Results
                    </h2>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $examReports->count() }} total</span>
                </div>
                
                <div class="space-y-4">
                    @forelse($examReports as $exam)
                        @php
                            $result = $exam->results->where('student_id', auth()->id())->first();
                        @endphp
                        <div class="border border-gray-200 rounded-xl p-5 flex justify-between items-center transition-all hover:shadow-sm">
                            <div class="flex-1">
                                <div class="font-medium text-lg mb-1">{{ $exam->title }}</div>
                                <div class="text-sm text-gray-600 mb-2">{{ $exam->description ?? '' }}</div>
                                <div class="flex flex-wrap gap-3 text-xs">
                                    <span class="bg-gray-100 text-gray-800 px-2.5 py-1 rounded-full flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Date: {{ \Carbon\Carbon::parse($exam->exam_date)->format('M j, Y') }}
                                    </span>
                                    @if($result)
                                        <span class="bg-green-100 text-green-800 px-2.5 py-1 rounded-full flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Completed
                                        </span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 px-2.5 py-1 rounded-full flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Pending
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($result)
                                <a href="{{ route('student.exams.result-card', $exam->id) }}" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-all shadow-sm">
                                    View Result
                                </a>
                            @else
                                <span class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">Not Taken</span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>No exams yet. 🎓</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-8">
            {{-- Classmates --}}
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Classmates
                </h3>
                
                @if($classmates->isEmpty())
                    <div class="text-center py-4 text-gray-500">
                        <p>No classmates loaded.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($classmates as $c)
                            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-all">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-800 flex items-center justify-center font-medium">
                                    {{ strtoupper(substr($c->student->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium truncate">{{ $c->student->name }}</div>
                                    <div class="text-xs text-gray-500">Roll: {{ $c->roll_no ?? '—' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Attendance --}}
            <div class="bg-white p-6 rounded-2xl shadow-md text-center">
                <h3 class="font-semibold text-lg mb-4 flex items-center gap-2 justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Attendance
                </h3>
                <p class="text-sm text-gray-600 mb-4">Check your attendance for this class.</p>
                <a href="{{ route('student.attendance.detail', ['classroom' => $classroom->id, 'section' => $sectionId]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-md transition-all text-sm font-medium">
                    View Attendance
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>

            {{-- Upcoming Events --}}
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Upcoming Events
                </h3>
                <div class="text-center py-8 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p>No upcoming events</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  const subjectButtons = Array.from(document.querySelectorAll('.subject-btn'));

  function setVisibility(selector, predicate) {
    const items = document.querySelectorAll(selector);
    let anyVisible = false;
    items.forEach(it => {
      if (predicate(it)) {
        it.classList.remove('hidden');
        anyVisible = true;
      } else {
        it.classList.add('hidden');
      }
    });
    return anyVisible;
  }

  function selectSubject(subjectId) {
    // highlight buttons
    subjectButtons.forEach(b => {
      if ((b.dataset.subjectId ?? '') === subjectId) {
        b.classList.remove('bg-gray-100', 'text-gray-800');
        b.classList.add('bg-indigo-600', 'text-white');
      } else {
        b.classList.remove('bg-indigo-600','text-white');
        b.classList.add('bg-gray-100', 'text-gray-800');
      }
    });

    if (!subjectId) {
      document.querySelectorAll('.assignment-item, .quiz-item').forEach(el => el.classList.remove('hidden'));
      document.getElementById('assignments-empty').classList.add('hidden');
      document.getElementById('quizzes-empty').classList.add('hidden');
      return;
    }

    const anyAssignments = setVisibility('.assignment-item', el => el.dataset.subjectId === subjectId);
    const anyQuizzes = setVisibility('.quiz-item', el => el.dataset.subjectId === subjectId);

    document.getElementById('assignments-empty').classList.toggle('hidden', anyAssignments);
    document.getElementById('quizzes-empty').classList.toggle('hidden', anyQuizzes);
  }

  subjectButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      const sid = btn.dataset.subjectId ?? '';
      selectSubject(sid);

      // update URL
      const url = new URL(window.location);
      if (sid) url.searchParams.set('subject_id', sid);
      else url.searchParams.delete('subject_id');
      window.history.pushState({}, '', url);
    });
  });

  // load subject from URL if exists
  const params = new URLSearchParams(window.location.search);
  const initialSubject = params.get('subject_id') || '';
  if (initialSubject) {
    const btn = subjectButtons.find(b => (b.dataset.subjectId ?? '') === initialSubject);
    if (btn) btn.click(); else subjectButtons[0]?.click();
  }
});
</script>
@endsection