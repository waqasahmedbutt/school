<aside class="w-64 bg-gray-700 text-white h-screen fixed left-0 top-0 shadow-lg">
    <div class="p-4 text-center font-bold text-xl border-b border-gray-600">
        Student Panel
    </div>

    <nav class="mt-4 space-y-2">
        <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 hover:bg-gray-600">📊 Dashboard</a>

        {{-- Academic --}}
        <div class="px-4 py-2 font-semibold text-gray-300">📚 My Learning</div>
        <a href="{{ route('student.classrooms') }}" class="block px-6 py-2 hover:bg-gray-600">My Classrooms</a>
        <a href="{{ route('student.assignments') }}" class="block px-6 py-2 hover:bg-gray-600">Assignments</a>
        <a href="{{ route('student.quizzes') }}" class="block px-6 py-2 hover:bg-gray-600">Quizzes</a>
        <a href="{{ route('student.exams') }}" class="block px-6 py-2 hover:bg-gray-600">Physical Exams</a>
        <a href="{{ route('student.attendance') }}" class="block px-6 py-2 hover:bg-gray-600">Attendance</a>
        <a href="{{ route('student.progress') }}" class="block px-6 py-2 hover:bg-gray-600">Progress Report</a>

        {{-- Communication --}}
        <div class="px-4 py-2 font-semibold text-gray-300">💬 Communication</div>
        <a href="{{ route('student.chat') }}" class="block px-6 py-2 hover:bg-gray-600">Chat with Teachers</a>
    </nav>
</aside>
