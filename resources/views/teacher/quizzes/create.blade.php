@extends('layouts.app')
@section('title', isset($quiz) ? 'Edit Quiz' : 'Create Quiz')

@section('content')
<div class="max-w-4xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">
    {{ isset($quiz) ? 'Edit Quiz' : 'Create Quiz' }} — {{ $classroom->name }}
  </h1>

  @if($errors->any())
    <div class="mb-4">
      <div class="bg-red-100 border border-red-300 text-red-800 p-3 rounded">
        <strong>There were some problems with your submission:</strong>
        <ul class="mt-2 list-disc list-inside">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  @php
    // Build initial questions structure: priority: old() -> $quiz (when editing) -> empty
    $initialQuestions = old('questions', []);
    if (empty($initialQuestions) && isset($quiz)) {
        $initialQuestions = [];
        foreach ($quiz->questions as $q) {
            $opts = [];
            foreach ($q->options as $o) {
                $opts[] = [
                    'text' => $o->option_text,
                    'is_correct' => $o->is_correct ? '1' : '0',
                ];
            }
            $initialQuestions[] = [
                'question' => $q->question,
                'marks' => $q->marks,
                'options' => $opts,
            ];
        }
    }
  @endphp

  <form action="{{ isset($quiz) ? route('teacher.quizzes.update', $quiz) : route('teacher.quizzes.store', $classroom) }}" method="POST" id="quiz-form">
    @csrf
    @if(isset($quiz))
      @method('PUT')
    @endif

    <div class="bg-white p-6 rounded shadow space-y-4">
      <div>
  <label class="block text-sm font-medium mb-1">Section & Subject</label>
  <select name="section_id" required class="border px-3 py-2 rounded w-full mb-2">
    <option value="">-- select section --</option>
    @foreach($sections as $s)
      <option value="{{ $s->section->id }}" 
        @if(old('section_id', $quiz->section_id ?? null) == $s->section->id) selected @endif>
        {{ $s->section->name }}
      </option>
    @endforeach
  </select>

  <select name="subject_id" required class="border px-3 py-2 rounded w-full">
    <option value="">-- select subject --</option>
    @foreach($sections as $s)
      <option value="{{ $s->subject->id ?? '' }}"
        @if(old('subject_id', $quiz->subject_id ?? null) == ($s->subject->id ?? null)) selected @endif>
        {{ $s->subject->name ?? '' }}
      </option>
    @endforeach
  </select>
</div>

      <div>
        <label class="block text-sm font-medium mb-1">Title</label>
        <input name="title" required class="border px-3 py-2 rounded w-full" value="{{ old('title', $quiz->title ?? '') }}" />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" class="border px-3 py-2 rounded w-full">{{ old('description', $quiz->description ?? '') }}</textarea>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Duration (minutes)</label>
        <input name="duration_minutes" type="number" class="border px-3 py-2 rounded w-48" value="{{ old('duration_minutes', $quiz->duration_minutes ?? '') }}" />
      </div>

      <hr>

      <div>
        <h3 class="font-semibold mb-2">Questions</h3>

        <div id="questions-wrapper" class="space-y-4"></div>

        <div class="mt-2">
          <button type="button" id="add-question" class="px-3 py-2 bg-green-600 text-white rounded">+ Add Question</button>
        </div>
      </div>

      <div class="flex justify-end">
        <a href="{{ route('teacher.classrooms.show', $classroom) }}" class="mr-2 px-3 py-2 bg-gray-200 rounded">Cancel</a>
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Save Quiz</button>
      </div>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const wrapper = document.getElementById('questions-wrapper');
  const addQBtn = document.getElementById('add-question');

  // initial questions from server (safe JSON)
  const initialQuestions = @json($initialQuestions);

  let qCounter = 0;

  function createOptionRow(qIndex, optIndex, optData = {}) {
    const row = document.createElement('div');
    row.className = 'flex gap-2 items-center';

    const textInput = document.createElement('input');
    textInput.type = 'text';
    textInput.name = `questions[${qIndex}][options][${optIndex}][text]`;
    textInput.placeholder = 'Option text';
    textInput.className = 'border px-3 py-2 rounded w-full';
    textInput.required = true;
    textInput.value = optData.text ?? '';

    // hidden input to ensure unchecked -> 0
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = `questions[${qIndex}][options][${optIndex}][is_correct]`;
    hidden.value = '0';

    const label = document.createElement('label');
    label.className = 'text-xs ml-2 flex items-center gap-2';

    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.name = `questions[${qIndex}][options][${optIndex}][is_correct]`;
    checkbox.value = '1';
    if (optData.is_correct === '1' || optData.is_correct === 1 || optData.is_correct === true) checkbox.checked = true;

    label.appendChild(checkbox);
    label.appendChild(document.createTextNode('correct'));

    // remove option button
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'ml-2 px-2 py-1 bg-red-500 text-white rounded text-sm';
    removeBtn.textContent = 'Remove';
    removeBtn.addEventListener('click', () => row.remove());

    row.appendChild(textInput);
    row.appendChild(hidden);
    row.appendChild(label);
    row.appendChild(removeBtn);

    return row;
  }

  function createQuestion(data = {}) {
    const idx = qCounter++;
    const card = document.createElement('div');
    card.className = 'border rounded p-3';

    // header
    const header = document.createElement('div');
    header.className = 'flex justify-between mb-2 items-center';
    const strong = document.createElement('strong');
    strong.textContent = 'Question';
    const removeQBtn = document.createElement('button');
    removeQBtn.type = 'button';
    removeQBtn.className = 'remove-question px-2 py-1 bg-red-500 text-white rounded text-sm';
    removeQBtn.textContent = 'Remove';
    removeQBtn.addEventListener('click', () => card.remove());

    header.appendChild(strong);
    header.appendChild(removeQBtn);

    // question input
    const qInput = document.createElement('input');
    qInput.type = 'text';
    qInput.name = `questions[${idx}][question]`;
    qInput.placeholder = 'Question text';
    qInput.className = 'border px-3 py-2 rounded w-full mb-2';
    qInput.required = true;
    qInput.value = data.question ?? '';

    // marks
    const marksDiv = document.createElement('div');
    marksDiv.className = 'flex gap-2 mb-2';
    const marksInput = document.createElement('input');
    marksInput.type = 'number';
    marksInput.name = `questions[${idx}][marks]`;
    marksInput.className = 'border px-3 py-2 rounded w-24';
    marksInput.placeholder = 'Marks';
    marksInput.value = data.marks ?? 1;
    marksDiv.appendChild(marksInput);

    // options container
    const optionsContainer = document.createElement('div');
    optionsContainer.className = 'options space-y-2 mb-2';

    const options = Array.isArray(data.options) && data.options.length ? data.options : [{}, {}];
    options.forEach((opt, i) => {
      const optRow = createOptionRow(idx, i, opt);
      optionsContainer.appendChild(optRow);
    });

    // add option button
    const addOptBtn = document.createElement('button');
    addOptBtn.type = 'button';
    addOptBtn.className = 'add-option px-2 py-1 bg-gray-200 rounded text-sm';
    addOptBtn.textContent = '+ Option';
    addOptBtn.addEventListener('click', () => {
      const optIdx = optionsContainer.children.length;
      const newRow = createOptionRow(idx, optIdx, {});
      optionsContainer.appendChild(newRow);
    });

    // assemble card
    card.appendChild(header);
    card.appendChild(qInput);
    card.appendChild(marksDiv);
    card.appendChild(optionsContainer);
    card.appendChild(addOptBtn);

    wrapper.appendChild(card);
  }

  addQBtn.addEventListener('click', () => createQuestion());

  // populate initial questions (from server-side old() / $quiz)
  if (Array.isArray(initialQuestions) && initialQuestions.length > 0) {
    initialQuestions.forEach(q => createQuestion(q));
  } else {
    // at least one question by default
    createQuestion();
  }
});
</script>
@endsection
