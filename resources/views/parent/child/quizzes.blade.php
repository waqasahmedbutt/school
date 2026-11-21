@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Quizzes — {{ $child->name }}</h1>
        <a href="{{ route('parent.child.overview', $child->id) }}" class="underline">Back</a>
    </div>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="text-left bg-gray-100">
                <th class="px-3 py-2">Subject</th>
                <th class="px-3 py-2">Quiz Title</th>
                <th class="px-3 py-2">Date</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Total Marks</th>
                <th class="px-3 py-2">Score</th>
                <th class="px-3 py-2">percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attempts as $a)
            <tr class="border-t">
                <td class="px-3 py-2">{{ $a->quiz->subject->name ?? '–' }}</td>
                <td class="px-3 py-2">{{ $a->quiz->title ?? '–' }}</td>
                <td class="px-3 py-2">{{ optional($a->finished_at)->format('Y-m-d') ?? optional($a->started_at)->format('Y-m-d') ?? '–' }}</td>
                <td class="px-3 py-2">{{ $a->finished_at ? 'Attempted' : 'Not Finished' }}</td>
                <td class="px-3 py-2">{{ $a->total_marks }}
</td>
                <td class="px-3 py-2">{{ $a->score ?? '–' }}</td>
                <td class="px-3 py-2">
    @if(isset($a->score) && isset($a->total_marks) && $a->total_marks != 0)
        {{ round(($a->score / $a->total_marks) * 100, 2) }}%
    @else
        –
    @endif
</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $attempts->links() }}
    </div>
</div>
@endsection
