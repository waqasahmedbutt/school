@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Classroom Tests — {{ $child->name }}</h1>
        <a href="{{ route('parent.child.overview', $child->id) }}" class="underline">Back</a>
    </div>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="text-left bg-gray-100">
                <th class="px-3 py-2">Subject</th>
                <th class="px-3 py-2">Test Name</th>
                <th class="px-3 py-2">Date</th>
                <th class="px-3 py-2">Marks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($marks as $m)
            <tr class="border-t">
                <td class="px-3 py-2">{{ $m->exam->subject->name ?? '–' }}</td>
                <td class="px-3 py-2">{{ $m->exam->title ?? '–' }}</td>
                <td class="px-3 py-2">{{ optional($m->exam->exam_date)->format('Y-m-d') ?? optional($m->created_at)->format('Y-m-d') }}</td>
                <td class="px-3 py-2">{{ $m->marks_obtained ?? '–' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $marks->links() }}
    </div>
</div>
@endsection

