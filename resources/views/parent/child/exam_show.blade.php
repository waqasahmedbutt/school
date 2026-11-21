@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">{{ $exam->title }} — {{ $child->name }}</h1>
        <a href="{{ route('parent.child.exams', $child->id) }}" class="underline">Back to Exams</a>
    </div>

    <div class="p-4 border rounded mb-4">
        <div class="text-sm text-gray-500">Exam Date: {{ optional($exam->exam_date)->format('Y-m-d') ?? '–' }}</div>
        <div class="text-lg font-semibold mt-2">Overall: {{ $percent ?? '–' }}% — Grade: {{ $grade ?? '–' }}</div>
    </div>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="text-left bg-gray-100">
                <th class="px-3 py-2">Subject</th>
                <th class="px-3 py-2">Marks Obtained</th>
                <th class="px-3 py-2">Total Marks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $r)
            <tr class="border-t">
                <td class="px-3 py-2">{{ $r->subject->name ?? '–' }}</td>
                <td class="px-3 py-2">{{ $r->marks_obtained?? 'not graded yet' }}</td>
                <td class="px-3 py-2">{{ $r->total_marks }}</td>
            </tr>
            @endforeach
            <tr class="font-semibold border-t">
                <td class="px-3 py-2">Total</td>
                <td class="px-3 py-2">{{ $totalObtained }}</td>
                <td class="px-3 py-2">{{ $totalPossible }}</td>
            </tr>
        </tbody>
    </table>
     <div class="mt-4">
      <a href="{{ route('student.exams.download', [$exam->id, $child->id]) }}" 
   class="px-4 py-2 bg-red-600 text-white rounded">
   Download PDF
</a>
    </div>
</div>
@endsection
