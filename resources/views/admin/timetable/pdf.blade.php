<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $classroom->name ?? 'School' }} - Timetable {{ isset($section) ? ' / Section '.$section->name : '' }}</title>
  <style>
    /* Basic PDF-safe styles */
    body { font-family: DejaVu Sans, Arial, sans-serif; margin: 12px; color: #222; }
    h1,h2 { margin:0; padding:0; }
    .header { text-align:center; margin-bottom:10px; }
    .meta { text-align:center; font-size:12px; color:#555; margin-bottom:14px; }

    table { width:100%; border-collapse: collapse; table-layout: fixed; }
    th, td { border: 1px solid #ddd; padding: 6px; vertical-align: middle; font-size:12px; word-wrap:break-word; }
    thead th { background:#1f77b4; color:#fff; text-align:center; font-weight:600; }
    .time-col { width:12%; text-align:center; font-weight:600; background:#f0f6fb; }

    /* Pleasant pastel palette (repeatable) */
    .slot { padding:6px; border-radius:4px; color:#111; font-weight:600; font-size:11px; }
    .slot-sub { display:block; font-weight:400; font-size:10px; margin-top:4px; color:#222; }
    .c1 { background:#f9e2e2; }  /* light red */
    .c2 { background:#eaf6e7; }  /* light green */
    .c3 { background:#fff1d6; }  /* light yellow */
    .c4 { background:#e7f0ff; }  /* light blue */
    .c5 { background:#f3eaff; }  /* light purple */
    .c6 { background:#e8f7f7; }  /* light teal */

    /* small caption */
    .small { font-size:10px; color:#666; margin-top:6px; }

    /* responsive-ish in PDF: truncate long names */
    .subject { display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100%; }
    .teacher { display:block; font-size:10px; color:#333; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  </style>
</head>
<body>
  <div class="header">
    <h1>{{ $classroom->name ?? 'School' }} {{ isset($section) ? ' - Section '.$section->name : '' }}</h1>
    <div class="meta">Class Timetable &nbsp; • &nbsp; Generated: {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</div>
  </div>

  @php
    // day names and default show order (Mon..Sat). Include Sunday only if there's data on Sun
    $dayNames = [0=>'Sun',1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat'];

    // Normalize days to ensure we have collections for each day index
    for($i=0;$i<=6;$i++) {
      if(!isset($days[$i])) $days[$i] = collect();
      // ensure it's a collection for convenience
      if(!is_a($days[$i], \Illuminate\Support\Collection::class)) $days[$i] = collect($days[$i]);
    }

    // find which top-days to show: prefer Mon..Sat; include Sunday only if has entries
    $topDays = [1,2,3,4,5,6];
    if($days[0]->isNotEmpty()) array_unshift($topDays, 0);

    // Build unique time-slots across all days (start|end) and sort by start_time
    $slots = [];
    foreach($days as $dayIndex => $col) {
      foreach($col as $entry) {
        $start = \Carbon\Carbon::parse($entry->start_time)->format('H:i');
        $end   = \Carbon\Carbon::parse($entry->end_time)->format('H:i');
        $key = $start . '|' . $end;
        $slots[$key] = ['start'=>$start,'end'=>$end];
      }
    }
    // sort slots by start time
    uasort($slots, function($a,$b){
      return strtotime($a['start']) <=> strtotime($b['start']);
    });
    // convert to indexed array
    $slots = array_values($slots);

    // helper to find entries for a day at a given slot
    function slotEntriesForDay($dayCollection, $slot) {
      return $dayCollection->filter(function($e) use($slot){
        return \Carbon\Carbon::parse($e->start_time)->format('H:i') === $slot['start']
            && \Carbon\Carbon::parse($e->end_time)->format('H:i') === $slot['end'];
      })->values();
    }

    // a palette cycle function (keeps visually distinct colors)
    function paletteClass($index) {
      $map = ['c1','c2','c3','c4','c5','c6'];
      return $map[$index % count($map)];
    }
  @endphp

  @if(empty($slots))
    <p>No timetable entries found.</p>
  @else
    <table>
      <thead>
        <tr>
          <th class="time-col">Time</th>
          @foreach($topDays as $d)
            <th>{{ $dayNames[$d] }}</th>
          @endforeach
        </tr>
      </thead>

      <tbody>
        @foreach($slots as $sIndex => $slot)
          <tr>
            <td class="time-col">{{ $slot['start'] }} - {{ $slot['end'] }}</td>

            @foreach($topDays as $dIndex => $d)
              @php
                $found = slotEntriesForDay($days[$d], $slot);
              @endphp

              <td>
                @if($found->isEmpty())
                  {{-- empty cell --}}
                  <div style="min-height:28px;"></div>
                @else
                  {{-- if there are multiple entries for same time slot on same day, stack them --}}
                  @foreach($found as $k => $entry)
                    @php $cls = paletteClass(($dIndex + $k)); @endphp
                    <div class="slot {{ $cls }}">
                      <span class="subject">{{ $entry->subject->name ?? 'Subject' }}</span>
                      <span class="slot-sub">{{ $entry->teacher->name ?? 'Teacher' }} @if($entry->room) • Rm {{ $entry->room }} @endif</span>
                    </div>
                  @endforeach
                @endif
              </td>
            @endforeach
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="small">
      <p><strong>Note:</strong> Period order is by start time. If a subject repeats across multiple days it will appear on those days. Export generated by SchoolSys.</p>
    </div>
  @endif

</body>
</html>
