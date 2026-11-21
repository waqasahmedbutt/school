@extends('layouts.app')
@section('title','Attendance Detail')

@section('content')
<div class="max-w-5xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-bold">Attendance — {{ $classroom->name }} / {{ $section->name }}</h1>
      <div class="text-sm text-gray-600">This class attendance: <strong>{{ $percent }}%</strong></div>
    </div>
    <div>
      <a href="{{ route('student.attendance.index') }}" class="px-3 py-2 bg-gray-100 rounded">Back</a>
    </div>
  </div>

  {{-- Subject selector --}}
  <div class="bg-white p-4 rounded shadow mb-4">
    <div class="flex items-center gap-2 flex-wrap">
      <a href="{{ route('student.attendance.detail', ['classroom' => $classroom->id, 'section' => $section->id]) }}"
         class="px-3 py-1 rounded {{ empty($subjectId) ? 'bg-indigo-600 text-white' : 'bg-gray-100' }}">All Subjects</a>

      @foreach($subjects as $s)
        <a href="{{ route('student.attendance.detail', ['classroom' => $classroom->id, 'section' => $section->id, 'subject_id' => $s['id']]) }}"
           class="px-3 py-1 rounded {{ (string)($subjectId ?? '') === (string)$s['id'] ? 'bg-indigo-600 text-white' : 'bg-gray-100' }}">
          {{ $s['name'] }}
          @if(!empty($s['teachers']))
            <span class="text-xs text-gray-500 ml-2">({{ $s['teachers']->map(fn($t) => $t['name'])->filter()->join(', ') }})</span>
          @endif
        </a>
      @endforeach
    </div>
  </div>

  {{-- Subject stats --}}
  <div class="grid md:grid-cols-3 gap-4 mb-4">
    @forelse($subjectStats as $stat)
      <div class="bg-white p-4 rounded shadow">
        <div class="font-semibold">{{ $stat->subject_name }}</div>
        <div class="text-sm text-gray-500 mt-1">
          Present: <strong class="text-green-600">{{ $stat->present }}</strong><br>
          Total: <strong>{{ $stat->total }}</strong><br>
          Attendance: <strong>{{ $stat->percent }}%</strong>
        </div>
      </div>
    @empty
      <div class="bg-white p-4 rounded shadow col-span-3">
        <p class="text-gray-500">No subject attendance available.</p>
      </div>
    @endforelse
  </div>

  {{-- Records table --}}
  <div class="bg-white p-4 rounded shadow">
    <table class="w-full table-auto">
      <thead>
        <tr class="text-left">
          <th class="p-2">Date</th>
          <th class="p-2">Subject / Teacher</th>
          <th class="p-2">Status</th>
          <th class="p-2">Notes</th>
        </tr>
      </thead>
      <tbody>
        @forelse($records as $r)
          <tr class="border-t">
            <td class="p-2">{{ \Carbon\Carbon::parse($r->date)->format('Y-m-d') }}</td>
            <td class="p-2">
              {{ optional($r->subject)->name ?? '—' }}
              <div class="text-xs text-gray-500">{{ optional($r->teacher)->name ?? '—' }}</div>
            </td>
            <td class="p-2">
              @if($r->status === 'present') <span class="text-green-600 font-semibold">Present</span>
              @elseif($r->status === 'absent') <span class="text-red-600 font-semibold">Absent</span>
              @elseif($r->status === 'late') <span class="text-yellow-600 font-semibold">Late</span>
              @else <span>{{ ucfirst($r->status) }}</span>
              @endif
            </td>
            <td class="p-2">{{ $r->notes }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="p-4 text-gray-500">No attendance records found for this filter.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-4">
      {{ $records->links() }}
    </div>
  </div>
</div>
@endsection
