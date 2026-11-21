@extends('layouts.app')
@section('title','Result Card')

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">{{ $exam->title }} - Result Card</h1>

  <div class="bg-white shadow rounded p-4">
    <h2 class="font-semibold mb-2">Student: {{ $student->name }}</h2>
    <table class="w-full text-left border">
      <thead>
        <tr class="border-b">
          <th class="p-2">Subject</th>
          <th class="p-2">Total Marks</th>
          <th class="p-2">Obtained</th>
        </tr>
      </thead>
      <tbody>
        @php $total = 0; $obtained = 0; @endphp
        @foreach($results as $res)
          @php 
            $total += $res->total_marks; 
            $obtained += $res->marks_obtained ?? 0;
          @endphp
          <tr class="border-b">
            <td class="p-2">{{ $res->subject->name }}</td>
            <td class="p-2">{{ $res->total_marks }}</td>
            <td class="p-2">{{ $res->marks_obtained ?? '—' }}</td>
          </tr>
        @endforeach
        <tr class="font-bold">
  <td class="p-2">Total</td>
  <td class="p-2">{{ $totalMarks }}</td>
  <td class="p-2">{{ $totalObtained }}</td>
</tr>
</tbody>
</table>

<p class="mt-4"><strong>Percentage:</strong> {{ $percentage }}%</p>
    <div class="mt-4">
      <a href="{{ route('student.exams.download', [$exam->id, $student->id]) }}" 
   class="px-4 py-2 bg-red-600 text-white rounded">
   Download PDF
</a>
    </div>
  </div>
</div>
@endsection
