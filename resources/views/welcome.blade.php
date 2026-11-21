<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>EduManage Pro - School Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.12.0/cdn.min.js" defer></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }
    .card-hover {
      transition: all 0.3s ease;
    }
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .feature-icon {
      transition: all 0.3s ease;
    }
    .feature-card:hover .feature-icon {
      transform: scale(1.1) rotate(5deg);
    }
    .btn-hover {
      transition: all 0.3s ease;
    }
    .btn-hover:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .animate-float {
      animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }
    .notification-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background: #ef4444;
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
    }
    .split-image {
      background-image: url("{{ asset('images/mainpage.jpg') }}");
      background-size: cover;
      background-position: center;
      min-height: 500px;
    }
    @media (max-width: 768px) {
      .split-image {
        min-height: 300px;
      }
    }
  </style>
</head>
<body class="antialiased">
  <!-- Header with navigation -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center py-4">
        <div class="flex items-center space-x-2">
          <div class="bg-blue-600 text-white p-2 rounded-lg">
            <i class="fas fa-graduation-cap text-xl"></i>
          </div>
          <h1 class="text-2xl font-bold text-gray-800">EduManage<span class="text-blue-600">Pro</span></h1>
        </div>
        
        <nav class="hidden md:flex space-x-8">
          <a href={{ route('home') }} class="text-gray-600 hover:text-blue-600 font-medium">Home</a>
          <a href={{ route('feature') }} class="text-gray-600 hover:text-blue-600 font-medium">Features</a>
          <a href={{ route('aboutus') }} class="text-gray-600 hover:text-blue-600 font-medium">About</a>
          <a href={{ route('contactus') }} class="text-gray-600 hover:text-blue-600 font-medium">Contact</a>
        </nav>
        
        <div class="flex items-center space-x-4">
          <div class="relative">
            
          </div>
          <a href="{{ route('login.form') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg btn-hover font-medium">Login</a>
          <a href="{{ route('register.form') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg btn-hover font-medium">Register</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="py-16 bg-gradient-to-r from-blue-500 to-indigo-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="md:w-1/2 mb-10 md:mb-0">
          <h2 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Modern School Management Solution</h2>
          <p class="text-xl mb-8 text-blue-100">Streamline administration, enhance learning, and connect everyone in your educational community.</p>
          <div class="flex space-x-4">
            <a href="{{ route('register.form') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold btn-hover">Get Started</a>
            <a href="#" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold btn-hover">Watch Demo</a>
          </div>
        </div>
        <div class="md:w-1/2 flex justify-center">
          <div class="relative">
           <div class="p-6 rounded-xl shadow-xl w-80 h-80 flex items-center justify-center animate-float 
            bg-cover bg-center text-center text-gray-800"
     style="background-image: url('{{ asset('images/mainpage2.jpg') }}');">
    
 
</div>

            <div class="absolute -bottom-4 -left-4 bg-yellow-400 text-gray-800 p-4 rounded-lg shadow-lg w-40">
              <div class="flex items-center">
                <i class="fas fa-chart-line mr-2"></i>
                <span class="font-semibold text-sm">+35% Efficiency</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- NEW: Split Screen Section -->
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row items-center">
        <!-- Image Section (Left Side) -->
        <div class="lg:w-1/2 w-full split-image rounded-xl shadow-lg overflow-hidden"></div>
        
        <!-- Text Content (Right Side) -->
        <div class="lg:w-1/2 w-full lg:pl-12 lg:py-0 py-8">
          <h2 class="text-3xl font-bold text-gray-800 mb-6">Transforming Education Through Technology</h2>
          <p class="text-gray-600 mb-6">Our innovative platform brings together all aspects of school management into one seamless experience, empowering educators and students alike.</p>
          
          <div class="space-y-4 mb-8">
            <div class="flex items-start">
              <div class="bg-blue-100 p-2 rounded-lg mr-4">
                <i class="fas fa-rocket text-blue-600 text-xl"></i>
              </div>
              <div>
                <h3 class="font-semibold text-lg text-gray-800">Streamlined Operations</h3>
                <p class="text-gray-600">Reduce administrative workload with automated processes and integrated systems.</p>
              </div>
            </div>
            
            <div class="flex items-start">
              <div class="bg-green-100 p-2 rounded-lg mr-4">
                <i class="fas fa-chart-pie text-green-600 text-xl"></i>
              </div>
              <div>
                <h3 class="font-semibold text-lg text-gray-800">Data-Driven Insights</h3>
                <p class="text-gray-600">Make informed decisions with comprehensive analytics and reporting tools.</p>
              </div>
            </div>
            
            <div class="flex items-start">
              <div class="bg-purple-100 p-2 rounded-lg mr-4">
                <i class="fas fa-users text-purple-600 text-xl"></i>
              </div>
              <div>
                <h3 class="font-semibold text-lg text-gray-800">Enhanced Collaboration</h3>
                <p class="text-gray-600">Connect teachers, students, and parents in a unified communication ecosystem.</p>
              </div>
            </div>
          </div>
          
          <a href="#" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold inline-flex items-center btn-hover">
            <span>Learn More About Our Approach</span>
            <i class="fas fa-arrow-right ml-2"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Comprehensive Features</h2>
        <p class="text-gray-600 max-w-3xl mx-auto">Our platform provides tailored solutions for every member of your educational institution</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Admin Card -->
        <div class="bg-gray-50 rounded-xl p-6 feature-card card-hover border border-gray-100">
          <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-cog text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">For Administrators</h3>
          <p class="text-gray-600">Manage users, classrooms, schedules, system settings and generate comprehensive reports.</p>
          <ul class="mt-4 space-y-2">
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">User Management</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">Attendance Tracking</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">Financial Reports</span>
            </li>
          </ul>
        </div>
        
        <!-- Teacher Card -->
        <div class="bg-gray-50 rounded-xl p-6 feature-card card-hover border border-gray-100">
          <div class="bg-green-100 text-green-600 w-16 h-16 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-chalkboard-teacher text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">For Teachers</h3>
          <p class="text-gray-600">Create assignments, track attendance, grade assignments, and communicate with students and parents.</p>
          <ul class="mt-4 space-y-2">
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">Gradebook Management</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">Lesson Planning</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">Parent Communication</span>
            </li>
          </ul>
        </div>
        
        <!-- Student Card -->
        <div class="bg-gray-50 rounded-xl p-6 feature-card card-hover border border-gray-100">
          <div class="bg-purple-100 text-purple-600 w-16 h-16 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-user-graduate text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">For Students</h3>
          <p class="text-gray-600">Access assignments, submit work, view grades and attendance, and collaborate with peers.</p>
          <ul class="mt-4 space-y-2">
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">Assignment Submission</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">Grade Tracking</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">Resource Access</span>
            </li>
          </ul>
        </div>
        
        <!-- Parent Card -->
        <div class="bg-gray-50 rounded-xl p-6 feature-card card-hover border border-gray-100">
          <div class="bg-yellow-100 text-yellow-600 w-16 h-16 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-users text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">For Parents</h3>
          <p class="text-gray-600">Monitor your child's progress, communicate with teachers, and access school announcements.</p>
          <ul class="mt-4 space-y-2">
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">Progress Reports</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">Teacher Messaging</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span class="text-sm">Attendance Alerts</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="py-16 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        <div>
          <div class="text-4xl font-bold mb-2">250+</div>
          <div class="text-sm uppercase tracking-wider">Schools Using</div>
        </div>
        <div>
          <div class="text-4xl font-bold mb-2">50k+</div>
          <div class="text-sm uppercase tracking-wider">Active Users</div>
        </div>
        <div>
          <div class="text-4xl font-bold mb-2">98%</div>
          <div class="text-sm uppercase tracking-wider">Satisfaction Rate</div>
        </div>
        <div>
          <div class="text-4xl font-bold mb-2">24/7</div>
          <div class="text-sm uppercase tracking-wider">Support</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">What Our Users Say</h2>
        <p class="text-gray-600 max-w-3xl mx-auto">Hear from administrators, teachers, students and parents about their experience</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-xl shadow-md card-hover">
          <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <i class="fas fa-user-tie text-blue-600"></i>
            </div>
            <div class="ml-4">
              <h4 class="font-semibold">Sarah Johnson</h4>
              <p class="text-sm text-gray-600">School Administrator</p>
            </div>
          </div>
          <p class="text-gray-600">"This system has transformed how we manage our school. Attendance tracking and report generation now takes minutes instead of hours."</p>
          <div class="flex mt-4">
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
          </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-md card-hover">
          <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
              <i class="fas fa-chalkboard-teacher text-green-600"></i>
            </div>
            <div class="ml-4">
              <h4 class="font-semibold">Michael Chen</h4>
              <p class="text-sm text-gray-600">Science Teacher</p>
            </div>
          </div>
          <p class="text-gray-600">"Grading assignments and communicating with parents has never been easier. The interface is intuitive and the features are exactly what teachers need."</p>
          <div class="flex mt-4">
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
          </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-md card-hover">
          <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
              <i class="fas fa-users text-purple-600"></i>
            </div>
            <div class="ml-4">
              <h4 class="font-semibold">Lisa Rodriguez</h4>
              <p class="text-sm text-gray-600">Parent</p>
            </div>
          </div>
          <p class="text-gray-600">"I love being able to see my children's progress in real-time. The notification system keeps me informed about important school events and assignments."</p>
          <div class="flex mt-4">
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
            <i class="fas fa-star text-yellow-400"></i>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-16 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <h2 class="text-3xl font-bold mb-6">Ready to Transform Your School Management?</h2>
      <p class="text-xl mb-8 max-w-3xl mx-auto">Join thousands of schools that have streamlined their operations and improved communication with EduManagePro</p>
      <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
        <a href="{{ route('register.form') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold btn-hover">Get Started Now</a>
        <a href="#" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold btn-hover">Request a Demo</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <h3 class="text-xl font-bold mb-4">EduManagePro</h3>
          <p class="text-gray-400">Comprehensive school management system for the modern educational institution.</p>
        </div>
        
        <div>
          <h4 class="font-semibold mb-4">Quick Links</h4>
          <ul class="space-y-2">
            <li><a href="#" class="text-gray-400 hover:text-white">Home</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white">Features</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white">Pricing</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
          </ul>
        </div>
        
        <div>
          <h4 class="font-semibold mb-4">Resources</h4>
          <ul class="space-y-2">
            <li><a href="#" class="text-gray-400 hover:text-white">Blog</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white">Documentation</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white">Support</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white">FAQ</a></li>
          </ul>
        </div>
        
        <div>
          <h4 class="font-semibold mb-4">Connect With Us</h4>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin-in"></i></a>
          </div>
          <div class="mt-4">
            <p class="text-gray-400">Email: info@edumanagepro.com</p>
            <p class="text-gray-400">Phone: +1 (555) 123-4567</p>
          </div>
        </div>
      </div>
      
      <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
        <p>&copy; 2023 EduManagePro. All rights reserved.</p>
      </div>
    </div>
  </footer>
</body>
</html>