@extends('layouts.app')
@section('title', 'Add Timetable Entries')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center mb-8">
            <div class="bg-indigo-100 p-3 rounded-xl mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Add Timetable Entries</h1>
                <p class="text-gray-600 mt-1">Create new schedule entries for classes</p>
            </div>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }} errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.timetable.store') }}" class="bg-white rounded-xl shadow-md overflow-hidden">
            @csrf
            
            <div class="p-6 md:p-8">
                <!-- Classroom Selection -->
                <div class="mb-8">
                    <label class="block text-lg font-medium text-gray-800 mb-3">Classroom & Section</label>
                    <div class="relative">
                        <select name="section_id" id="section_id" class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                            <option value="">-- choose classroom / section --</option>
                            @foreach($classrooms as $c)
                                <optgroup label="{{ $c->name }}">
                                    @foreach($c->sections as $s)
                                        <option value="{{ $s->id }}"
                                            @if(isset($selectedSection) && $selectedSection == $s->id) selected
                                            @elseif(isset($selectedClassroom) && $selectedClassroom == $c->id && !isset($selectedSection)) selected
                                            @endif
                                        >Section {{ $s->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Timetable Entries -->
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 md:mb-0">Timetable Entries</h2>
                        <div class="flex space-x-3">
                            <button type="button" id="add-row" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Row
                            </button>
                            <button type="button" id="remove-row" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Remove Last
                            </button>
                        </div>
                    </div>

                    <div id="entries-wrapper" class="space-y-6">
                        <template id="entry-template">
                            <div class="timetable-row bg-gray-50 border border-gray-200 rounded-lg p-6 shadow-sm">
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                        <select name="entries[IDX][subject_id]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                                            @foreach($subjects as $sub)
                                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Teacher</label>
                                        <select name="entries[IDX][teacher_id]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                                            @foreach($teachers as $t)
                                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Days</label>
                                        <div class="days-container flex flex-wrap gap-2 mt-1">
                                            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $k => $v)
                                            <div class="day-option relative">
                                                <input type="checkbox" id="day-{{ $k }}-IDX" name="entries[IDX][days][]" value="{{ $k }}" class="day-checkbox absolute opacity-0">
                                                <label for="day-{{ $k }}-IDX" class="day-label inline-block px-3 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium cursor-pointer hover:bg-gray-200 transition-colors">{{ $v }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="entries[IDX][days][]" value="" disabled>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                        <input type="time" name="entries[IDX][start_time]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                        <input type="time" name="entries[IDX][end_time]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Room (Optional)</label>
                                    <input type="text" name="entries[IDX][room]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" placeholder="Enter room number">
                                </div>
                            </div>
                        </template>

                        <!-- Initial row -->
                        <div class="timetable-row bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                    <select name="entries[0][subject_id]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                                        @foreach($subjects as $sub)
                                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Teacher</label>
                                    <select name="entries[0][teacher_id]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                                        @foreach($teachers as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Days</label>
                                    <div class="days-container flex flex-wrap gap-2 mt-1">
                                        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $k => $v)
                                        <div class="day-option relative">
                                            <input type="checkbox" id="day-{{ $k }}-0" name="entries[0][days][]" value="{{ $k }}" class="day-checkbox absolute opacity-0">
                                            <label for="day-{{ $k }}-0" class="day-label inline-block px-3 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium cursor-pointer hover:bg-gray-200 transition-colors">{{ $v }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="entries[0][days][]" value="" disabled>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                    <input type="time" name="entries[0][start_time]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                    <input type="time" name="entries[0][end_time]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Room (Optional)</label>
                                <input type="text" name="entries[0][room]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" placeholder="Enter room number">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="pt-6 border-t border-gray-200 mt-8">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.timetable.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Entries
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .day-checkbox:checked + .day-label {
        background-color: #6366f1;
        color: white;
    }
    
    .timetable-row {
        transition: all 0.3s ease;
        border-left: 4px solid #6366f1;
    }
    
    .timetable-row:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease forwards;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let idx = 1;
        const entriesWrapper = document.getElementById('entries-wrapper');
        const template = document.getElementById('entry-template');
        
        // Add row functionality
        document.getElementById('add-row').addEventListener('click', function() {
            const html = template.innerHTML.replace(/IDX/g, idx);
            const div = document.createElement('div');
            div.innerHTML = html;
            div.classList.add('fade-in');
            entriesWrapper.appendChild(div.firstElementChild);
            idx++;
        });
        
        // Remove row functionality
        document.getElementById('remove-row').addEventListener('click', function() {
            const rows = document.querySelectorAll('.timetable-row');
            if (rows.length > 1) {
                rows[rows.length - 1].remove();
                if (idx > 1) idx--;
            }
        });
        
        // Initialize day checkboxes
        document.querySelectorAll('.day-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Enable/disable the hidden input based on whether any checkboxes are checked
                const hiddenInput = this.closest('.timetable-row').querySelector('input[type="hidden"][name$="[days][]"]');
                const anyChecked = Array.from(this.closest('.days-container').querySelectorAll('.day-checkbox')).some(cb => cb.checked);
                
                if (anyChecked) {
                    hiddenInput.setAttribute('disabled', 'disabled');
                } else {
                    hiddenInput.removeAttribute('disabled');
                }
            });
        });
    });
</script>
@endsection