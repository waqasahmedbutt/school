@extends('layouts.app')
@section('title','Marks')

@section('content')
<div class="max-w-6xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">📊 Physical Exam Marks</h1>

  <div class="bg-white p-4 rounded shadow">
    @if($exams->isEmpty())
      <p class="text-gray-500">No exams available.</p>
    @else
      <table class="min-w-full border border-gray-200 rounded-lg">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 text-left">Exam</th>
            <th class="p-2 text-left">Classroom / Section</th>
            <th class="p-2 text-left">Subject</th>
            <th class="p-2 text-left">Date</th>
            <th class="p-2 text-left">Total Marks</th>
            <th class="p-2 text-left">My Marks</th>
            <th class="p-2 text-left">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($exams as $exam)
            <tr class="border-t">
              <td class="p-2">{{ $exam->title }}</td>
              <td class="p-2">{{ $exam->classroom->name ?? 'N/A' }} - {{ $exam->section->name ?? 'N/A' }}</td>
              <td class="p-2">{{ $exam->subject->name ?? 'N/A' }}</td>
              <td class="p-2">{{ $exam->exam_date->format('d M, Y') }}</td>
              <td class="p-2">{{ $exam->total_marks }}</td>
              <td class="p-2">
                {{ optional($exam->myMark)->marks_obtained ?? 'Not given' }}
              </td>
              <td class="p-2">
                <a href="{{ route('student.marks.exam', $exam->id) }}" 
                   class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                  View
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
