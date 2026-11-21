@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Assignments — {{ $child->name }}</h1>
        <a href="{{ route('parent.child.overview', $child->id) }}" class="underline">Back</a>
    </div>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="text-left bg-gray-100">
                <th class="px-3 py-2">Subject</th>
                <th class="px-3 py-2">Title</th>
                <th class="px-3 py-2">Assigned</th>
                <th class="px-3 py-2">Due Date</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Total Marks</th>
                <th class="px-3 py-2">Marks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assignments as $a)
            @php
                $sub = $submissions[$a->id] ?? null;
            @endphp
            <tr class="border-t">
                <td class="px-3 py-2">{{ $a->subject->name ?? '–' }}</td>
                <td class="px-3 py-2">{{ $a->title }}</td>
                <td class="px-3 py-2">{{ optional($a->created_at)->format('Y-m-d') }}</td>
                <td class="px-3 py-2">{{ optional($a->due_at)->format('Y-m-d') ?? '–' }}</td>
                <td class="px-3 py-2">{{ $sub ? 'Submitted' : 'Not Submitted' }}</td>
                <td class="px-3 py-2">{{ $a->total_marks }}</td>
                <td class="px-3 py-2">{{ $sub ? ($sub->marks_awarded ?? '–') : '–' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
