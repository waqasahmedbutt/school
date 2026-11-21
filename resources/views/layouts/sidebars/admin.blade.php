<aside class="w-64 bg-gray-900 text-white h-screen fixed left-0 top-0 shadow-lg flex flex-col">
    <!-- Logo / Title -->
    <div class="p-4 text-center font-bold text-2xl border-b border-gray-700">
        🏫 SMS Admin
    </div>

    <!-- Navigation -->
    <nav class="flex-1 mt-4 overflow-y-auto">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-4 py-2 hover:bg-gray-700 transition duration-200">
            📊 <span class="ml-2">Dashboard</span>
        </a>

        <!-- User Management -->
        <div class="px-4 py-2 mt-4 text-gray-400 uppercase text-xs font-semibold">User Management</div>
        <a href="{{ route('admin.users') }}" class="block px-6 py-2 hover:bg-gray-700">👥 All Users</a>
        <a href="{{ route('admin.teachers') }}" class="block px-6 py-2 hover:bg-gray-700">👨‍🏫 Teachers</a>
        <a href="{{ route('admin.students') }}" class="block px-6 py-2 hover:bg-gray-700">🎓 Students</a>
        <a href="{{ route('admin.parents') }}" class="block px-6 py-2 hover:bg-gray-700">👪 Parents</a>
        <a href="{{ route('admin.staff') }}" class="block px-6 py-2 hover:bg-gray-700">🧑‍💼 Staff</a>

        <!-- Academics -->
        <div class="px-4 py-2 mt-4 text-gray-400 uppercase text-xs font-semibold">Academics</div>
        <a href="{{ route('admin.subjects') }}" class="block px-6 py-2 hover:bg-gray-700">📚 Subjects</a>
        <a href="{{ route('admin.classrooms') }}" class="block px-6 py-2 hover:bg-gray-700">🏫 Classrooms</a>
        <a href="{{ route('admin.sections') }}" class="block px-6 py-2 hover:bg-gray-700">🅰️ Sections</a>
        <a href="{{ route('admin.timetables') }}" class="block px-6 py-2 hover:bg-gray-700">🗓 Timetables</a>
        <a href="{{ route('admin.teacher-assignments') }}" class="block px-6 py-2 hover:bg-gray-700">📌 Teacher Assignments</a>

        <!-- Exams & Attendance -->
        <div class="px-4 py-2 mt-4 text-gray-400 uppercase text-xs font-semibold">Exams & Records</div>
        <a href="{{ route('admin.exams') }}" class="block px-6 py-2 hover:bg-gray-700">📝 Exams</a>
        <a href="{{ route('admin.attendance') }}" class="block px-6 py-2 hover:bg-gray-700">✅ Attendance</a>
        <a href="{{ route('admin.results') }}" class="block px-6 py-2 hover:bg-gray-700">📊 Results</a>

        <!-- Settings -->
        <div class="px-4 py-2 mt-4 text-gray-400 uppercase text-xs font-semibold">System</div>
        <a href="{{ route('admin.settings') }}" class="block px-6 py-2 hover:bg-gray-700">⚙️ Settings</a>
        <a href="{{ route('logout') }}" class="block px-6 py-2 hover:bg-red-600 mt-2">🚪 Logout</a>
    </nav>
</aside>
