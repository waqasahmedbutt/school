@extends('layouts.app')
@section('title','Timetables')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📅 Timetables</h1>
            <p class="text-sm text-gray-500 mt-1">Sections with timetable entries. Click "View" to see the full schedule.</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.timetable.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
                Add Period
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-4 items-end bg-white p-4 rounded shadow">
        <div>
            <label class="block text-sm font-medium text-gray-700">Classroom</label>
            <select name="classroom_id" class="border rounded px-3 py-2 w-full" onchange="this.form.submit()">
                <option value="">-- All Classrooms --</option>
                @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" @selected((int)($classroomId??0) === $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        @if(!empty($classroomId))
            <div>
                <label class="block text-sm font-medium text-gray-700">Section</label>
                <select name="section_id" class="border rounded px-3 py-2 w-full" onchange="this.form.submit()">
                    <option value="">-- All Sections --</option>
                    @php $selectedClass = $classrooms->firstWhere('id',$classroomId); @endphp
                    @if($selectedClass)
                        @foreach($selectedClass->sections as $s)
                            <option value="{{ $s->id }}" @selected((int)($sectionId??0) === $s->id)>Section {{ $s->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        @endif

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.timetable.index') }}" class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm">Reset</a>
        </div>
    </form>

    {{-- Timetable Table --}}
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left text-gray-600 uppercase tracking-wide">Classroom</th>
                    <th class="p-3 text-left text-gray-600 uppercase tracking-wide">Section</th>
                    <th class="p-3 text-center text-gray-600 uppercase tracking-wide">Entries</th>
                    <th class="p-3 text-left text-gray-600 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sections as $s)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-3 font-medium text-gray-800">{{ $s->classroom->name ?? '-' }}</td>
                        <td class="p-3 text-gray-700">Section {{ $s->name }}</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold @if($s->timetableEntries()->count() > 0) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                {{ $s->timetableEntries()->count() }}
                            </span>
                        </td>
                        <td class="p-3 flex flex-wrap gap-2">
                            <a href="{{ route('admin.timetable.show', ['classroom' => $s->classroom_id, 'section' => $s->id]) }}" class="px-3 py-1 bg-blue-100 text-blue-700 rounded shadow hover:bg-blue-200 text-sm">View</a>

                            <a href="{{ route('admin.timetable.create', ['classroom' => $s->classroom_id, 'section' => $s->id]) }}" class="px-3 py-1 bg-green-100 text-green-700 rounded shadow hover:bg-green-200 text-sm">Add Period</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-500">No sections with timetable entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $sections->appends(request()->query())->links() }}
    </div>
</div>
@endsection
