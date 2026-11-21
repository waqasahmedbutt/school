@extends('layouts.app')
@section('title', $quiz->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 space-y-6">
  <h1 class="text-2xl font-bold">{{ $quiz->title }}</h1>

  <div class="text-sm text-gray-500">
    Duration: <span class="font-medium">{{ $quiz->duration_minutes }} mins</span> |
    Total Marks: <span class="font-medium">{{ $totalMarks }}</span>
  </div>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
  @endif
  @if(session('info'))
    <div class="bg-blue-50 text-blue-800 p-3 rounded mb-4">{{ session('info') }}</div>
  @endif

  {{-- If attempt finished -> show result --}}
@if($attempt && $attempt->finished_at)
  <div class="bg-white p-4 rounded shadow mb-4">
    <div class="font-medium">
      You scored: {{ $attempt->score }} / {{ $quiz->questions->sum('marks') }} 
      ({{ round(($attempt->score / max($quiz->questions->sum('marks'),1)) * 100, 2) }}%)
    </div>
    <div class="text-sm text-gray-500">
      Attempted on {{ $attempt->finished_at->format('Y-m-d H:i') }}
    </div>
  </div>

  <div class="bg-white p-6 rounded-xl shadow space-y-4">
    <h3 class="text-lg font-bold mb-2">Your Answers</h3>

    @php
      $answers = collect($attempt->answers ?? []);
      $answersByQ = $answers->mapWithKeys(fn($a) => [$a['question_id'] => $a['option_id']]);
    @endphp

    @foreach($quiz->questions as $qIndex => $q)
      <div class="border rounded-lg p-4">
        <div class="font-medium text-gray-800 mb-2">
          Q{{ $qIndex + 1 }}. {{ $q->question }}
          <span class="text-sm text-gray-500">({{ $q->marks }} pts)</span>
        </div>

        @foreach($q->options as $opt)
          @php
            $selected = $answersByQ[$q->id] ?? null;
            $isCorrect = (bool) $opt->is_correct;
          @endphp
          <div class="flex items-center gap-2 mb-1">
            <span class="{{ $isCorrect ? 'text-green-700 font-semibold' : '' }}">
              {{ $opt->option_text }}
            </span>

            @if($selected == $opt->id)
              <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">Your Answer</span>
            @endif

            @if($isCorrect)
              <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Correct</span>
            @endif
          </div>
        @endforeach
      </div>
    @endforeach
  </div>




  {{-- If no attempt yet -> show Start button --}}
  @elseif(!$attempt)
    <div class="bg-white p-6 rounded-xl shadow">
      <p class="text-gray-700 mb-4">{{ $quiz->description }}</p>
      <form action="{{ route('student.quizzes.start', $quiz) }}" method="POST">
        @csrf
        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded">Start Quiz</button>
      </form>
    </div>

  {{-- Attempt exists and not finished -> show form with timer --}}
  @else
    <div class="bg-white p-6 rounded-xl shadow" oncopy="return false;" oncut="return false;" onpaste="return false;">
      <p class="text-gray-600 mb-3">{{ $quiz->description }}</p>

      <div id="timer" class="font-bold text-red-600 mb-4 text-lg">⏳ Preparing timer...</div>

      <form id="quizForm" action="{{ route('student.quizzes.submit', $quiz) }}" method="POST">
        @csrf

        <div class="space-y-6">
          @foreach($quiz->questions as $q)
            <div class="border rounded-lg p-4 shadow-sm bg-gray-50">
              <div class="font-medium text-gray-800 mb-2">{{ $loop->iteration }}. {{ $q->question }}
                <span class="text-sm text-gray-500"> ({{ $q->marks ?? 0 }} pts)</span>
              </div>

              {{-- hidden once per question so answers array always includes question_id --}}
              <input type="hidden" name="answers[{{ $q->id }}][question_id]" value="{{ $q->id }}">

              <div class="mt-2 space-y-2">
                @foreach($q->options as $opt)
                  <label class="block text-gray-700">
                    <input
                      type="radio"
                      name="answers[{{ $q->id }}][option_id]"
                      value="{{ $opt->id }}"
                      class="mr-2"
                      {{-- pre-select if student already answered earlier attempt (optional) --}}
                      @if(!empty($attempt->answers))
                        @php
                          $prev = collect($attempt->answers)->firstWhere('question_id', $q->id);
                        @endphp
                        @if(isset($prev['option_id']) && $prev['option_id'] == $opt->id)
                          checked
                        @endif
                      @endif
                    >
                    {{ $opt->option_text }}
                  </label>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>

        <div class="mt-6 text-right">
          <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Submit</button>
        </div>
      </form>
    </div>
  @endif
</div>

{{-- Timer script only when attempt exists and not finished --}}
@if($attempt && !$attempt->finished_at)
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // remainingSeconds passed from controller; fallback to quiz duration if missing
    let duration = {{ isset($remainingSeconds) ? (int)$remainingSeconds : ($quiz->duration_minutes * 60) }};
    const timerEl = document.getElementById('timer');
    const form = document.getElementById('quizForm');

    function formatTime(s) {
      const m = Math.floor(s / 60);
      const sec = s % 60;
      return `${m}:${sec.toString().padStart(2, '0')}`;
    }

    if (duration <= 0) {
      // time already over on server — auto-submit minimal data
      timerEl.textContent = '⏳ Time is up. Submitting...';
      // submit after a short delay to allow UI to update
      setTimeout(() => form.submit(), 700);
      return;
    }

    timerEl.textContent = `⏳ Time left: ${formatTime(duration)}`;

    const countdown = setInterval(() => {
      duration--;
      timerEl.textContent = `⏳ Time left: ${formatTime(duration)}`;
      if (duration <= 0) {
        clearInterval(countdown);
        timerEl.textContent = '⏳ Time is up. Auto-submitting...';
        // small delay for UX
        setTimeout(() => form.submit(), 700);
      }
    }, 1000);

    // prevent using back button to avoid accidental leaving
    history.pushState(null, null, location.href);
    window.onpopstate = function () { history.go(1); };
  });
</script>
@endif
@endsection
