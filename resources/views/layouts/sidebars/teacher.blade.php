<aside class="w-64 bg-gray-800 text-white h-screen fixed left-0 top-0 shadow-lg">
    <div class="p-4 text-center font-bold text-xl border-b border-gray-700">
        Teacher Panel
    </div>

    <nav class="mt-4 space-y-2">
        <a href="{{ route('teacher.dashboard') }}" class="block px-4 py-2 hover:bg-gray-700">📊 Dashboard</a>

        {{-- Classroom --}}
        <div class="px-4 py-2 font-semibold text-gray-300">🏫 My Classrooms</div>
        <a href="{{ route('teacher.classrooms') }}" class="block px-6 py-2 hover:bg-gray-700">View Classrooms</a>
        <a href="{{ route('teacher.timetable') }}" class="block px-6 py-2 hover:bg-gray-700">My Timetable</a>

        {{-- Academic --}}
        <div class="px-4 py-2 font-semibold text-gray-300">📚 Academic</div>
        <a href="{{ route('teacher.assignments') }}" class="block px-6 py-2 hover:bg-gray-700">Assignments</a>
        <a href="{{ route('teacher.quizzes') }}" class="block px-6 py-2 hover:bg-gray-700">Online Quizzes</a>
        <a href="{{ route('teacher.exams') }}" class="block px-6 py-2 hover:bg-gray-700">Physical Exams</a>
        <a href="{{ route('teacher.attendance') }}" class="block px-6 py-2 hover:bg-gray-700">Mark Attendance</a>

        {{-- Communication --}}
        <div class="px-4 py-2 font-semibold text-gray-300">💬 Communication</div>
        <a href="{{ route('teacher.chat') }}" class="block px-6 py-2 hover:bg-gray-700">Chat with Students/Parents</a>
    </nav>
</aside>
