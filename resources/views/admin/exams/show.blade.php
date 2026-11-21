@extends('layouts.app')
@section('title', 'Exam — ' . $exam->title)

@section('content')
<div class="max-w-6xl mx-auto space-y-4">

    {{-- Exam Header --}}
    <div class="bg-white p-6 rounded shadow">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold">{{ $exam->title }}</h1>
                <div class="text-sm text-gray-500 mt-1">{{ $exam->description ?? '—' }}</div>
                <div class="text-xs text-gray-500 mt-2">Classroom: {{ $exam->classroom->name ?? '—' }}</div>
                <div class="text-xs text-gray-500">Exam date: {{ optional($exam->exam_date)->format('Y-m-d') }}</div>
            </div>

            <div class="text-right">
                <div class="text-sm text-gray-500">Sections: {{ $sections->count() }}</div>
                <div class="text-sm text-gray-500">Students: {{ $students->count() }}</div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.exams.index') }}" class="px-3 py-2 bg-gray-200 rounded">Back to Exams</a>
        </div>
    </div>

    {{-- Section Selector --}}
    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Select Section</h2>
        <form method="GET">
            <select name="section_id" onchange="this.form.submit()" class="border px-3 py-2 rounded">
                @foreach($sections as $sec)
                    <option value="{{ $sec->id }}" {{ $sectionId == $sec->id ? 'selected' : '' }}>
                        {{ $sec->name }}
                    </option>
                @endforeach
            </select>
            <noscript>
                <button class="ml-2 px-3 py-1 bg-blue-600 text-white rounded">Go</button>
            </noscript>
        </form>
    </div>

    {{-- Students List --}}
    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Students in Section {{ $sections->firstWhere('id', $sectionId)->name ?? '—' }}</h2>

        @if($students->isEmpty())
            <p class="text-gray-500">No students found in this section.</p>
        @else
            <table class="w-full text-left border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">Name</th>
                        <th class="p-2 border">Roll No</th>
                        <th class="p-2 border">Email</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $cs)
                        <tr class="border-b">
                            <td class="p-2 border">{{ $cs->student->name }}</td>
                            <td class="p-2 border">{{ $cs->roll_no ?? '—' }}</td>
                            <td class="p-2 border">{{ $cs->student->email }}</td>
                            <td class="p-2 border">
                                <a href="{{ route('admin.students.examResult', [$cs->student->id, $exam->id]) }}"
                                   class="px-2 py-1 bg-blue-600 text-white rounded text-sm">
                                   View Result
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
