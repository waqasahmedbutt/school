<aside class="w-64 bg-gray-600 text-white h-screen fixed left-0 top-0 shadow-lg">
    <div class="p-4 text-center font-bold text-xl border-b border-gray-500">
        Parent Panel
    </div>

    <nav class="mt-4 space-y-2">
        <a href="{{ route('parent.dashboard') }}" class="block px-4 py-2 hover:bg-gray-500">📊 Dashboard</a>

        {{-- Children --}}
        <div class="px-4 py-2 font-semibold text-gray-300">👨‍👩‍👧 Children</div>
        <a href="{{ route('parent.children') }}" class="block px-6 py-2 hover:bg-gray-500">My Children</a>
        <a href="{{ route('parent.reports') }}" class="block px-6 py-2 hover:bg-gray-500">Progress Reports</a>
        <a href="{{ route('parent.exams') }}" class="block px-6 py-2 hover:bg-gray-500">Marksheets</a>
        <a href="{{ route('parent.attendance') }}" class="block px-6 py-2 hover:bg-gray-500">Attendance</a>

        {{-- Communication --}}
        <div class="px-4 py-2 font-semibold text-gray-300">💬 Communication</div>
        <a href="{{ route('parent.chat') }}" class="block px-6 py-2 hover:bg-gray-500">Chat with Teachers</a>
    </nav>
</aside>
