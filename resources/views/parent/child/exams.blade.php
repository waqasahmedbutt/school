@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Exams — {{ $child->name }}</h1>
        <a href="{{ route('parent.child.overview', $child->id) }}" class="underline">Back</a>
    </div>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="text-left bg-gray-100">
                <th class="px-3 py-2">Exam Name</th>
                <th class="px-3 py-2">Date</th>
                <th class="px-3 py-2">Subjects</th>
                <th class="px-3 py-2">Overall %</th>
                <th class="px-3 py-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exams as $ex)
            @php
                $res = $myResults[$ex->id] ?? null;
            @endphp
            <tr class="border-t">
                <td class="px-3 py-2">{{ $ex->title }}</td>
                <td class="px-3 py-2">{{ optional($ex->exam_date)->format('Y-m-d') ?? '–' }}</td>
                <td class="px-3 py-2">{{ $subjectCounts[$ex->id] ?? 0 }}</td>
                <td class="px-3 py-2">
                   @if(isset($myResults[$ex->id]))
    @php
        $r = $myResults[$ex->id];
        $obtained = $r->sum('marks_obtained');
        $total = $r->sum('total_marks');
        $percent = $total ? round(($obtained / $total) * 100, 2) : null;
    @endphp
    {{ $percent ?? '–' }}%
@else
    –
@endif

                </td>
                <td class="px-3 py-2">
                    <a href="{{ route('parent.child.exams.show', ['child' => $child->id, 'exam' => $ex->id]) }}" class="underline">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $exams->links() }}
    </div>
</div>
@endsection
