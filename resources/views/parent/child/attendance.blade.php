@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Attendance — {{ $child->name }}</h1>
        <a href="{{ route('parent.child.overview', $child->id) }}" class="underline">Back to Overview</a>
    </div>

    <form method="get" class="mb-4 flex gap-2 items-end">
        <label class="text-sm">From
            <input type="date" name="from" value="{{ $from }}" class="ml-2 border p-1 rounded">
        </label>
        <label class="text-sm">To
            <input type="date" name="to" value="{{ $to }}" class="ml-2 border p-1 rounded">
        </label>
        <button class="px-3 py-1 bg-blue-600 text-white rounded">Filter</button>
    </form>

    <div class="mb-4 p-4 border rounded bg-gray-50">
        <div>Present: <strong>{{ $presentCount }}</strong> / Total Days: <strong>{{ $totalDays }}</strong></div>
        <div>Percentage: <strong>{{ $attendancePct ?? '–' }}%</strong></div>
    </div>

    <div>
        @foreach($records as $date => $items)
            <div class="border-b py-3">
                <div class="font-medium">{{ $date }}</div>

                {{-- if attendance has subject relation, show subject-wise, otherwise show single record status --}}
                @if($items->first()->relationLoaded('subject') || isset($items->first()->subject_id))
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-2">
                        @foreach($items as $rec)
                            <div class="p-2 border rounded">
                                <div class="text-sm">{{ $rec->subject->name ?? 'Subject' }}</div>
                                <div class="font-semibold">{{ ucfirst($rec->status) }}</div>
                                @if($rec->remarks)
                                    <div class="text-xs text-gray-500">{{ $rec->remarks }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-2">
                        <div>Status: <strong>{{ ucfirst($items->first()->status) }}</strong></div>
                        @if($items->first()->remarks)
                            <div class="text-sm text-gray-500">{{ $items->first()->remarks }}</div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
