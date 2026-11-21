@extends('layouts.app')
@section('title','Exam Detail')

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Exam Detail</h1>

  <div class="bg-white p-6 rounded shadow">
    <p><strong>Exam:</strong> {{ $exam->title }}</p>
    <p><strong>Classroom:</strong> {{ $exam->classroom->name ?? 'N/A' }}</p>
    <p><strong>Section:</strong> {{ $exam->section->name ?? 'N/A' }}</p>
    <p><strong>Subject:</strong> {{ $exam->subject->name ?? 'N/A' }}</p>
    <p><strong>Date:</strong> {{ $exam->exam_date->format('d M, Y') }}</p>
    <p><strong>Total Marks:</strong> {{ $exam->total_marks }}</p>
    <p><strong>Your Marks:</strong> {{ $mark->marks_obtained ?? 'Not given' }}</p>
    <p><strong>Remarks:</strong> {{ $mark->remarks ?? 'No remarks' }}</p>
  </div>

  <div class="mt-4">
    <a href="{{ route('student.marks.index') }}" 
       class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
      Back to Marks
    </a>
  </div>
</div>
@endsection
