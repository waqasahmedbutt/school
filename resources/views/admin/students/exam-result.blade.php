@extends('layouts.app')
@section('title', 'Exam Result — '.$student->name)

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Exam Result: {{ $exam->title }}</h1>
    <p>Student: {{ $student->name }} (Roll: {{ $rollNo ?? '—' }})</p>
    <p>Section: {{ $section->name ?? '—' }}</p>

    <table class="w-full mt-4 border">
        <thead>
            <tr class="border-b">
                <th class="p-2">Subject</th>
                <th class="p-2">Marks Obtained</th>
                <th class="p-2">Total Marks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $res)
                <tr class="border-b">
                    <td class="p-2">{{ $res->subject->name ?? '—' }}</td>
                    <td class="p-2">{{ $res->marks_obtained ?? '—' }}</td>
                    <td class="p-2">{{ $res->total_marks ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-2 text-gray-500">No results found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="mt-4 font-semibold">Total: {{ $totalObtained }} / {{ $totalMarks }} ({{ $percentage }}%)</p>
<div class="mt-4">
      <a href="{{ route('student.exams.download', [$exam->id, $student->id]) }}" 
   class="px-4 py-2 bg-red-600 text-white rounded">
   Download PDF
</a>
    </div>
    <a href="{{ route('admin.exams.show', $exam) }}" class="mt-4 inline-block px-3 py-2 bg-gray-200 rounded">Back to Exam</a>
</div>
@endsection
