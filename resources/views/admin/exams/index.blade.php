@extends('layouts.app')
@section('title','Exams')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Exams</h1>
            <p class="text-sm text-gray-500 mt-1">Manage exams and their schedules.</p>
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto">
            <form method="GET" class="flex flex-col sm:flex-row gap-2 items-center w-full md:w-auto">
                <select name="classroom_id" class="border rounded px-3 py-2 text-sm">
                    <option value="">All Classrooms</option>
                    @foreach(App\Models\Classroom::orderBy('name')->get() as $c)
                        <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title" class="border rounded px-3 py-2 w-full sm:w-64 text-sm">

                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded text-sm hover:bg-gray-900">Filter</button>
            </form>

            <a href="{{ route('admin.exams.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
                + New Exam
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        @if($exams->isEmpty())
            <div class="p-6 text-center text-gray-500">No exams found.</div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classroom</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($exams as $exam)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ $exam->title }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $exam->classroom->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ optional($exam->exam_date)->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.exams.show', $exam) }}" class="px-2 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">View</a>
                            <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('Delete this exam?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-4">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
