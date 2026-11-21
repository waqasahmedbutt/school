<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $exam->title }} - Result Card</title>
  <style>
    /* dompdf-friendly styles (use DejaVu Sans) */
    @page { margin: 16mm 12mm; }
    body { font-family: "DejaVu Sans", sans-serif; color: #222; font-size: 12px; margin: 0; }
    .card { padding: 18px; }

    /* Header (uses table for reliable PDF layout) */
    .hdr-table { width:100%; border-collapse:collapse; margin-bottom:12px; }
    .hdr-left { vertical-align: middle; width:120px; }
    
    .hdr-center { text-align:center; vertical-align:middle; }
    .school-name { font-size:18px; font-weight:800; letter-spacing:0.5px; text-transform:uppercase; color:#0b63c6; }
    .school-sub { font-size:11px; color:#444; margin-top:6px; }

    .hdr-right { text-align:right; vertical-align:middle; width:180px; }
    .exam-title { font-weight:700; color:#0b63c6; font-size:13px; }
    .meta { font-size:11px; color:#444; margin-top:6px; }

    /* Student / Class info */
    .info-grid { width:100%; border-collapse:collapse; margin-top:8px; }
    .info-cell { width:50%; padding:10px; border:1px solid #e6eef9; background:#fbfdff; vertical-align:top; }
    .info-dt { display:inline-block; width:110px; font-weight:700; color:#333; }
    .info-dd { display:inline-block; color:#222; }

    /* Marks table */
    .marks { width:100%; border-collapse:collapse; margin-top:14px; }
    .marks th, .marks td { border:1px solid #d7e1f5; padding:8px 10px; text-align:center; }
    .marks th { background:#f3f8ff; color:#0b63c6; font-weight:700; }
    .marks td.subject { text-align:left; padding-left:12px; color:#111; }

    .row-alt { background:#ffffff; }
    .row-alt-2 { background:#fbfdff; }

    /* Summary & remarks */
    .summary-wrap { display:table; width:100%; margin-top:12px; }
    .summary-left { display:table-cell; width:42%; padding:12px; border:1px solid #e6eef9; background:#ffffff; vertical-align:top; }
    .summary-right { display:table-cell; padding-left:12px; vertical-align:top; }

    .big-num { font-size:22px; font-weight:800; color:#0b63c6; }
    .grade-pill { display:inline-block; padding:6px 10px; border-radius:6px; background:#edf9f1; color:#2b8a3e; font-weight:800; margin-top:6px; }

    .remarks { margin-top:8px; padding:10px; border-radius:6px; background:#fbfdff; border:1px solid #eef4ff; color:#333; font-size:12px; }

    /* Footer */
    .footer { width:100%; margin-top:30px; display:table; }
    .sig { display:table-cell; width:55%; vertical-align:bottom; }
    .stamp-cell { display:table-cell; width:45%; text-align:right; vertical-align:bottom; }
    .sig-line { width:220px; border-top:1px solid #999; margin-top:36px; color:#666; text-align:center; font-size:11px; }
    .stamp-img { width:140px; height:auto; border-radius:6px; object-fit:contain; border:0px; }

    .small { font-size:10px; color:#666; margin-top:6px; }

    /* small print */
    .note { margin-top:8px; font-size:10px; color:#666; }
  </style>
</head>
<body>
  @php
    // embed image helper (base64) - safe for dompdf
    

    // safe totals
    $totalObtained = $totalObtained ?? ($results->sum('marks_obtained') ?? 0);
    $totalMarks    = $totalMarks ?? ($results->sum('total_marks') ?? 0);
    $overallPercent = $totalMarks > 0 ? ($totalObtained / $totalMarks) * 100 : 0;
    $overallPercentFormatted = number_format($overallPercent, 2);
    // determine final grade
    if ($overallPercent >= 90) $finalGrade = 'A+';
    elseif ($overallPercent >= 80) $finalGrade = 'A';
    elseif ($overallPercent >= 70) $finalGrade = 'B';
    elseif ($overallPercent >= 60) $finalGrade = 'C';
    elseif ($overallPercent >= 40) $finalGrade = 'D';
    else $finalGrade = 'F';
  @endphp

  <div class="card">
    <!-- Header -->
    <table class="hdr-table">
      <tr>
        <td class="hdr-left">
         
            
       
        </td>

        <td class="hdr-center">
          <div class="school-name">{{ $exam->classroom->school->name ?? 'EduSchoolPro' }}</div>
          <div class="school-sub">{{ $exam->classroom->school->motto ?? '' }}</div>
          <div style="margin-top:6px; font-size:12px; color:#444;">Address: {{ $exam->classroom->school->address ?? '' }}</div>
        </td>

        <td class="hdr-right">
          <div class="exam-title">{{ $exam->title }}</div>
          <div class="meta">Exam Date: {{ optional($exam->exam_date)->format('Y-m-d') ?? '—' }}</div>
          <div class="meta">Academic Year: {{ $exam->academic_year ?? date('Y') }}</div>
        </td>
      </tr>
    </table>

    <!-- Student + Class Info -->
    <table class="info-grid">
      <tr>
        <td class="info-cell">
          <div><span class="info-dt">Student Name:</span> <span class="info-dd">{{ $student->name }}</span></div>
          <div><span class="info-dt">Roll No:</span> <span class="info-dd">{{ $rollNo ?? 'N/A' }}</span></div>
          <div><span class="info-dt">Student ID:</span> <span class="info-dd">{{ $student->id }}</span></div>
        </td>
        <td class="info-cell">
          <div><span class="info-dt">Classroom:</span> <span class="info-dd">{{ $exam->classroom->name ?? '—' }}</span></div>
          <div><span class="info-dt">Section:</span> <span class="info-dd">{{ $section->name ?? '—' }}</span></div>
          <div><span class="info-dt">Date Issued:</span> <span class="info-dd">{{ now()->format('Y-m-d') }}</span></div>
        </td>
      </tr>
    </table>

    <!-- Marks Table -->
    <table class="marks" role="table" aria-label="Marks">
      <thead>
        <tr>
          <th style="text-align:left; padding-left:12px;">Subject</th>
          <th>Full Marks</th>
          <th>Obtained</th>
          <th>Percentage</th>
          <th>Grade</th>
        </tr>
      </thead>
      <tbody>
        @foreach($results as $i => $res)
          @php
            $marks = is_null($res->marks_obtained) ? null : (float) $res->marks_obtained;
            $total = is_null($res->total_marks) ? null : (float) $res->total_marks;
            $subPercent = ($total && $marks !== null) ? round(($marks / $total) * 100, 2) : null;
            if ($subPercent === null) $subGrade = '-';
            else {
              if ($subPercent >= 90) $subGrade = 'A+';
              elseif ($subPercent >= 80) $subGrade = 'A';
              elseif ($subPercent >= 70) $subGrade = 'B';
              elseif ($subPercent >= 60) $subGrade = 'C';
              elseif ($subPercent >= 50) $subGrade = 'D';
              else $subGrade = 'F';
            }
            $rowClass = $i % 2 ? 'row-alt-2' : 'row-alt';
          @endphp
          <tr class="{{ $rowClass }}">
            <td class="subject">{{ $res->subject->name ?? '—' }}</td>
            <td>{{ $total ? intval($total) : '-' }}</td>
            <td>{{ is_null($marks) ? '-' : intval($marks) }}</td>
            <td>{{ $subPercent !== null ? $subPercent.'%' : '-' }}</td>
            <td>{{ $subGrade }}</td>
          </tr>
        @endforeach

        <!-- Totals row -->
        <tr style="background:#f7fbff; font-weight:700;">
          <td style="text-align:left; padding-left:12px;">Total</td>
          <td>{{ intval($totalMarks) }}</td>
          <td>{{ intval($totalObtained) }}</td>
          <td>{{ $totalMarks > 0 ? number_format($overallPercent, 2). '%' : '0%' }}</td>
          <td>{{ $finalGrade }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Summary + Remarks -->
    <div class="summary-wrap">
      <div class="summary-left">
        <div style="font-size:12px; color:#555;">Total Obtained</div>
        <div class="big-num" style="margin-top:6px;">{{ intval($totalObtained) }} / {{ intval($totalMarks) }}</div>
        <div class="grade-pill">{{ number_format($overallPercent, 2) }}% — {{ $finalGrade }}</div>
        <div class="note">Overall performance based on published marks for this exam.</div>
      </div>

      <div class="summary-right">
        <div style="font-weight:700; margin-bottom:6px;">Remarks</div>
        <div class="remarks">
          @if($overallPercent >= 85)
            Outstanding performance. Keep it up!
          @elseif($overallPercent >= 70)
            Good performance. Keep improving.
          @elseif($overallPercent >= 50)
            Satisfactory — work harder to improve.
          @else
            Unsatisfactory — please consult your teacher/guardian.
          @endif

          <div style="margin-top:8px; font-size:11px; color:#444;">
            <strong>Note:</strong> This result is system generated and valid without signature.
          </div>
        </div>
      </div>
    </div>

    <!-- Footer: signature + stamp -->
    <div class="footer" style="margin-top:20px;">
      <div class="sig">
        <div class="sig-line">Principal / Headmaster</div>
        <div class="small">Signature &amp; Date</div>
      </div>

      <div class="stamp-cell">
        
        <img src="{{ public_path('images/Edu.jpeg') }}" alt="Stamp" style="width:140px; height:auto;" />
       
      </div>
    </div>

  </div>
</body>
</html>
