@extends('layouts.app')
@section('title', 'Timetable — ' . ($classroom->name ?? '') . ' / ' . ($section->name ?? ''))

@section('content')
<div class="flex items-center justify-between mb-4">
  <div>
    <h1 class="text-2xl font-bold">Timetable — {{ $classroom->name }} / Section {{ $section->name }}</h1>
    <p class="text-sm text-gray-600">Entries ordered by start time. Use export to download PDF.</p>
  </div>

  <div class="flex gap-2">
    <a href="{{ route('admin.timetable.export', ['classroom_id' => $classroom->id, 'section_id' => $section->id]) }}" class="px-3 py-2 bg-green-600 text-white rounded">⬇ Export PDF</a>
    <a href="{{ route('admin.timetable.create', ['classroom' => $classroom->id, 'section' => $section->id]) }}" class="px-3 py-2 bg-blue-600 text-white rounded">➕ Add</a>
    <a href="{{ route('admin.timetable.index') }}" class="px-3 py-2 bg-gray-500 text-white rounded">← Back</a>
  </div>
</div>

@if(session('success'))
  <div class="mb-3 p-2 bg-green-50 border border-green-200 text-green-700 rounded">{{ session('success') }}</div>
@endif

{{-- Days grid --}}
<div class="space-y-6">
  @php $dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; @endphp

  @foreach($days as $d => $entries)
    <div class="bg-white rounded shadow">
      <div class="border-b p-3 flex justify-between items-center">
        <div class="font-semibold">{{ $dayNames[$d] }}</div>
        <div class="text-sm text-gray-500">{{ $entries->count() }} period(s)</div>
      </div>

      @if($entries->isEmpty())
        <div class="p-4 text-gray-500">No periods for this day.</div>
      @else
        <div class="p-3">
          <table class="w-full text-sm">
            <thead class="text-left bg-gray-100">
              <tr>
                <th class="p-2">Time</th>
                <th class="p-2">Subject</th>
                <th class="p-2">Teacher</th>
                <th class="p-2">Room</th>
                <th class="p-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($entries as $entry)
                <tr class="border-t">
                  <td class="p-2">{{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($entry->end_time)->format('H:i') }}</td>
                  <td class="p-2">{{ $entry->subject->name ?? '-' }}</td>
                  <td class="p-2">{{ $entry->teacher->name ?? '-' }}</td>
                  <td class="p-2">{{ $entry->room ?? '-' }}</td>
                  <td class="p-2 flex gap-2">
                    <a href="{{ route('admin.timetable.edit', ['timetable' => $entry->id]) }}" class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded">Edit</a>

                    <form action="{{ route('admin.timetable.destroy', ['timetable' => $entry->id]) }}" method="POST" onsubmit="return confirm('Delete this period?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="px-2 py-1 bg-red-100 text-red-700 rounded">Delete</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  @endforeach
</div>
@endsection
