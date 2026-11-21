<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Dashboard') — Eduschool Pro</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script>
    // Simple JS for mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
      const menuButton = document.getElementById('mobile-menu-button');
      const sidebar = document.getElementById('sidebar');
      
      if (menuButton && sidebar) {
        menuButton.addEventListener('click', function() {
          sidebar.classList.toggle('hidden');
          sidebar.classList.toggle('mobile-sidebar');
        });
      }
    });
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    body {
      font-family: 'Inter', sans-serif;
    }
    
    .gradient-bg {
      background: linear-gradient(135deg, #4F46E5 0%, #7E22CE 100%);
    }
    
    .sidebar-link {
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
    }
    
    .sidebar-link:hover, .sidebar-link.active {
      border-left-color: currentColor;
      background-color: rgba(99, 102, 241, 0.1);
    }
    
    .stat-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
      .mobile-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        z-index: 50;
        width: 75%;
        max-width: 300px;
        transform: translateX(0);
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        animation: slideIn 0.3s ease-out;
      }
      
      @keyframes slideIn {
        from { transform: translateX(-100%); }
        to { transform: translateX(0); }
      }
      
      .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 40;
      }
    }
  </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex flex-col">
    
    <!-- Top Navbar -->
    <nav class="gradient-bg text-white shadow-lg px-4 py-3 sticky top-0 z-30">
      <div class="flex items-center justify-between">
        <!-- Logo and mobile menu button -->
        <div class="flex items-center">
          <button id="mobile-menu-button" class="md:hidden mr-3 text-white focus:outline-none">
            <i class="fas fa-bars text-xl"></i>
          </button>
          <a href="{{ route('dashboard') }}" class="font-bold text-xl tracking-wide flex items-center">
            <i class="fas fa-graduation-cap mr-2"></i> Eduschool Pro
          </a>
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-4">
          <!-- Notifications -->
          <div class="relative">
            
          </div>
          
          @auth
            <div class="hidden md:flex items-center space-x-2 bg-white/20 px-3 py-1 rounded-full">
              <span class="text-sm">{{ auth()->user()->name }}</span>
              <span class="text-xs bg-indigo-800 px-2 py-1 rounded-full capitalize">{{ auth()->user()->role }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="text-sm bg-white/20 text-white px-3 py-1 rounded-md hover:bg-white/30 transition flex items-center">
                <i class="fas fa-sign-out-alt mr-1"></i> Logout
              </button>
            </form>
          @else
            <a href="{{ route('login.form') }}" class="text-sm bg-white text-indigo-700 px-3 py-1 rounded-md hover:bg-gray-100 transition">Login</a>
            <a href="{{ route('register.form') }}" class="text-sm bg-indigo-800 text-white px-3 py-1 rounded-md hover:bg-indigo-900 transition">Register</a>
          @endauth
        </div>
      </div>
    </nav>

    <div class="flex flex-1 relative">
      <!-- Sidebar Overlay (mobile only) -->
      <div id="sidebar-overlay" class="sidebar-overlay hidden" onclick="document.getElementById('sidebar').classList.add('hidden')"></div>
      
      <!-- Sidebar -->
      <aside id="sidebar" class="hidden md:block w-64 bg-white border-r shadow-sm p-4 z-20">
        <h2 class="text-lg font-semibold mb-6 text-gray-700 flex items-center">
          <i class="fas fa-th-large mr-2 text-indigo-600"></i> Main Menu
        </h2>
        <ul class="space-y-1 text-gray-600 font-medium">

          {{-- ================= ADMIN ================= --}}
          @if(auth()->user()->role === 'admin')
            <li><a href="{{ route('admin.dashboards') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-indigo-600"><i class="fas fa-chart-pie mr-3"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-indigo-600"><i class="fas fa-users mr-3"></i> Manage Users</a></li>
            <li><a href="{{ route('admin.classrooms.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-indigo-600"><i class="fas fa-school mr-3"></i> Classrooms</a></li>
            <li><a href="{{ route('admin.subjects.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-indigo-600"><i class="fas fa-book mr-3"></i> Subjects</a></li>
            <li><a href="{{ route('admin.timetable.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-indigo-600"><i class="fas fa-calendar-alt mr-3"></i> Timetables</a></li>
            <li><a href="{{ route('admin.schools.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-indigo-600"><i class="fas fa-university mr-3"></i> Schools</a></li>
            <li><a href="{{ route('admin.exams.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-indigo-600"><i class="fas fa-cog mr-3"></i> Exam</a></li>

          {{-- ================= TEACHER ================= --}}
          @elseif(auth()->user()->role === 'teacher')
            <li><a href="{{ route('teacher.dashboard') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-green-600"><i class="fas fa-chart-pie mr-3"></i> Dashboard</a></li>
            <li><a href="{{ route('teacher.classes.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-green-600"><i class="fas fa-school mr-3"></i> My Classes</a></li>
            <li><a href="{{ route('teacher.assignments.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-green-600"><i class="fas fa-tasks mr-3"></i> Assignments</a></li>
            <li><a href="{{ route('teacher.quizzes.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-green-600"><i class="fas fa-question-circle mr-3"></i> Quizzes</a></li>
            <li><a href="{{ route('teacher.attendance.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-green-600"><i class="fas fa-clipboard-check mr-3"></i> Attendance</a></li>
            <li><a href="{{ route('teacher.notifications.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-green-600"><i class="fas fa-bell mr-3"></i> Notifications</a></li>

          {{-- ================= STUDENT ================= --}}
          @elseif(auth()->user()->role === 'student')
            <li><a href='/student' class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-purple-600"><i class="fas fa-chart-pie mr-3"></i> Dashboard</a></li>
            <li><a href='/student/classrooms' class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-purple-600"><i class="fas fa-chalkboard mr-3"></i> My Classroom</a></li>
            <li><a href="{{ route('student.assignments.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-purple-600"><i class="fas fa-tasks mr-3"></i> My Assignments</a></li>
            <li><a href="{{ route('student.quizzes.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-purple-600"><i class="fas fa-question-circle mr-3"></i> My Quizzes</a></li>
            <li><a href="{{ route('student.marks.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-purple-600"><i class="fas fa-trophy mr-3"></i> Classroom Test</a></li>
            <li><a href="{{ route('student.attendance.index') }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-purple-600"><i class="fas fa-clipboard-check mr-3"></i> Attendance</a></li>
   

          {{-- ================= PARENT ================= --}}
  @elseif(auth()->user()->role === 'parent')
    @php
        $parent = auth()->user();
        // first child fallback
        $firstChild = $parent->children()->first();

        // If current route has a child parameter (route-model bound or id), prefer it
        $routeChild = request()->route('child');

        if ($routeChild) {
            // routeChild can be a User model or an id — normalize to a User model
            if (is_object($routeChild)) {
                $currentChild = $routeChild;
            } else {
                $currentChild = \App\Models\User::find($routeChild);
            }
        } else {
            $currentChild = $firstChild;
        }

        // route fallbacks (update names if your route names differ)
        $childrenIndex = route('parent.children.index');
        $childOverview = $currentChild ? route('parent.child.overview', $currentChild->id) : $childrenIndex;
        $childAttendance = $currentChild ? route('parent.child.attendance', $currentChild->id) : $childrenIndex;
        $childAssignments = $currentChild ? route('parent.child.assignments', $currentChild->id) : $childrenIndex;
        $childQuizzes = $currentChild ? route('parent.child.quizzes', $currentChild->id) : $childrenIndex;
        $childPhysical = $currentChild ? route('parent.child.physical_exams', $currentChild->id) : $childrenIndex;
        $childExams = $currentChild ? route('parent.child.exams', $currentChild->id) : $childrenIndex;
    @endphp

    <li>
        <a href="{{ $childrenIndex }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-pink-600">
            <i class="fas fa-chart-pie mr-3"></i> Dashboard
        </a>
    </li>

    <li>
        <a href="{{ $childrenIndex }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-pink-600">
            <i class="fas fa-child mr-3"></i> My Children
        </a>
    </li>

    <li>
        <a href="{{ $childExams }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-pink-600">
            <i class="fas fa-file-alt mr-3"></i> MarkSheets
        </a>
    </li>

    <li>
        <a href="{{ $childOverview }}" class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-pink-600">
            <i class="fas fa-chart-line mr-3"></i> Progress
        </a>
    </li>

    {{-- Quick links header + child switcher (click any child to switch quick-links target) --}}
    <li class="mt-2 text-xs text-gray-500 px-3">Children</li>

    @foreach($parent->children()->get() as $c)
        <li>
            <a href="{{ route('parent.child.overview', $c->id) }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg hover:text-pink-600 {{ $currentChild && $currentChild->id === $c->id ? 'bg-gray-100 font-semibold' : '' }}">
                <i class="fas fa-user-graduate mr-3"></i> {{ $c->name }}
            </a>
        </li>
    @endforeach

    @if($currentChild)
        <li class="mt-2 text-xs text-gray-500 px-3">Quick links ({{ $currentChild->name }})</li>

        <li>
            <a href="{{ $childOverview }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg hover:text-pink-600">
                <i class="fas fa-user-graduate mr-3"></i> Overview
            </a>
        </li>

        <li>
            <a href="{{ $childAttendance }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg hover:text-pink-600">
                <i class="fas fa-calendar-check mr-3"></i> Attendance
            </a>
        </li>

        <li>
            <a href="{{ $childAssignments }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg hover:text-pink-600">
                <i class="fas fa-book mr-3"></i> Assignments
            </a>
        </li>

        <li>
            <a href="{{ $childQuizzes }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg hover:text-pink-600">
                <i class="fas fa-question-circle mr-3"></i> Quizzes
            </a>
        </li>

        <li>
            <a href="{{ $childPhysical }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg hover:text-pink-600">
                <i class="fas fa-flask mr-3"></i> Classroom Tests
            </a>
        </li>

        <li>
            <a href="{{ $childExams }}" class="sidebar-link flex items-center px-3 py-2 rounded-lg hover:text-pink-600">
                <i class="fas fa-clipboard-list mr-3"></i> Exams
            </a>
        </li>
    @endif
     <li><a href='/parent/chat' class="sidebar-link flex items-center px-3 py-3 rounded-lg hover:text-purple-600"><i class="fas fa-chart-pie mr-3"></i> Chat</a></li>
@endif


          <!-- Logout (all roles) -->
          <li class="pt-4 mt-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit"
                class="w-full flex items-center justify-center px-3 py-2 rounded-lg bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 transition shadow-md">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
              </button>
            </form>
          </li>
        </ul>
      </aside>

      <!-- Main Content -->
      <main class="flex-1 p-4 md:p-6">
        <!-- Dashboard Header -->
       
        
        <!-- Stats Overview -->
        
        <!-- Main Content Area -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
          @yield('content')
        </div>
      </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t py-4 shadow-inner mt-8">
      <div class="max-w-7xl mx-auto text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} <span class="font-semibold text-indigo-600">Eduschool Pro</span>. All rights reserved.
      </div>
    </footer>
  </div>
  
  <script>
    // Enhanced mobile menu functionality
    document.addEventListener('DOMContentLoaded', function() {
      const menuButton = document.getElementById('mobile-menu-button');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebar-overlay');
      
      function toggleSidebar() {
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('mobile-sidebar');
        overlay.classList.toggle('hidden');
      }
      
      if (menuButton) {
        menuButton.addEventListener('click', toggleSidebar);
      }
      
      if (overlay) {
        overlay.addEventListener('click', toggleSidebar);
      }
    });
  </script>
</body>
</html>