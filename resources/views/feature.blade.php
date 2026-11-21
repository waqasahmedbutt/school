<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Features - EduManage Pro</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
    .tab-active {
      border-bottom: 3px solid #3b82f6;
      color: #3b82f6;
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

  <!-- Page Header -->
  <section class="py-12 bg-gradient-to-r from-blue-500 to-indigo-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <h1 class="text-4xl font-bold mb-4">Powerful Features for Modern Education</h1>
      <p class="text-xl max-w-3xl mx-auto">Discover how EduManagePro can transform your educational institution with our comprehensive suite of tools</p>
    </div>
  </section>

  <!-- Tabs Navigation -->
  <section class="py-8 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-wrap justify-center gap-6">
        <a href="#administrators" class="text-lg font-medium py-2 tab-active">Administrators</a>
        <a href="#teachers" class="text-lg font-medium py-2 text-gray-600 hover:text-blue-600">Teachers</a>
        <a href="#students" class="text-lg font-medium py-2 text-gray-600 hover:text-blue-600">Students</a>
        <a href="#parents" class="text-lg font-medium py-2 text-gray-600 hover:text-blue-600">Parents</a>
      </div>
    </div>
  </section>

  <!-- Administrators Features -->
  <section id="administrators" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Features for Administrators</h2>
        <p class="text-gray-600 max-w-3xl mx-auto">Streamline operations and manage your institution efficiently with our powerful administrative tools</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-xl shadow-md card-hover">
          <div class="bg-blue-100 text-blue-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-users-cog text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">User Management</h3>
          <p class="text-gray-600">Easily manage students, teachers, and staff with our comprehensive user management system.</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-md card-hover">
          <div class="bg-blue-100 text-blue-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-calendar-alt text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Scheduling</h3>
          <p class="text-gray-600">Create and manage class schedules, room assignments, and academic calendars with ease.</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-md card-hover">
          <div class="bg-blue-100 text-blue-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-chart-bar text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Reporting & Analytics</h3>
          <p class="text-gray-600">Generate detailed reports on attendance, grades, finances, and other key metrics.</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-md card-hover">
          <div class="bg-blue-100 text-blue-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-money-bill-wave text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Financial Management</h3>
          <p class="text-gray-600">Track tuition payments, expenses, and generate financial reports for your institution.</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-md card-hover">
          <div class="bg-blue-100 text-blue-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-database text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Data Management</h3>
          <p class="text-gray-600">Securely store and manage all institutional data with robust backup and recovery options.</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-md card-hover">
          <div class="bg-blue-100 text-blue-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-tasks text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Attendance Tracking</h3>
          <p class="text-gray-600">Monitor and record student and staff attendance with automated reporting features.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Teachers Features -->
  <section id="teachers" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Features for Teachers</h2>
        <p class="text-gray-600 max-w-3xl mx-auto">Enhance your teaching experience with tools designed to save time and improve student engagement</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="bg-gray-50 p-6 rounded-xl shadow-md card-hover">
          <div class="bg-green-100 text-green-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-book-open text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Gradebook Management</h3>
          <p class="text-gray-600">Easily record, calculate, and track student grades with our intuitive gradebook system.</p>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-xl shadow-md card-hover">
          <div class="bg-green-100 text-green-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-clipboard-list text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Assignment Creation</h3>
          <p class="text-gray-600">Create and distribute assignments with rubrics, due dates, and attachment support.</p>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-xl shadow-md card-hover">
          <div class="bg-green-100 text-green-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-comments text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Parent Communication</h3>
          <p class="text-gray-600">Easily communicate with parents about student progress, events, and concerns.</p>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-xl shadow-md card-hover">
          <div class="bg-green-100 text-green-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-chalkboard text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Lesson Planning</h3>
          <p class="text-gray-600">Create, organize, and share lesson plans with other teachers in your department.</p>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-xl shadow-md card-hover">
          <div class="bg-green-100 text-green-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-chart-line text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Progress Tracking</h3>
          <p class="text-gray-600">Monitor student progress over time and identify areas where additional support is needed.</p>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-xl shadow-md card-hover">
          <div class="bg-green-100 text-green-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 feature-icon">
            <i class="fas fa-file-alt text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Report Generation</h3>
          <p class="text-gray-600">Generate progress reports and report cards with customizable templates.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-16 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <h2 class="text-3xl font-bold mb-6">Ready to Explore All Features?</h2>
      <p class="text-xl mb-8 max-w-3xl mx-auto">See how EduManagePro can transform your educational institution with a personalized demo</p>
      <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
        <a href="{{ route('register.form') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold btn-hover">Get Started Now</a>
        <a href="contact.html" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold btn-hover">Request a Demo</a>
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
            <li><a href="index.html" class="text-gray-400 hover:text-white">Home</a></li>
            <li><a href="features.html" class="text-gray-400 hover:text-white">Features</a></li>
            <li><a href="about.html" class="text-gray-400 hover:text-white">About</a></li>
            <li><a href="contact.html" class="text-gray-400 hover:text-white">Contact</a></li>
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

  <script>
    // Simple tab functionality
    document.addEventListener('DOMContentLoaded', function() {
      const tabLinks = document.querySelectorAll('a[href^="#"]');
      
      tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Remove active class from all tabs
          tabLinks.forEach(tab => {
            tab.classList.remove('tab-active', 'text-blue-600');
            tab.classList.add('text-gray-600');
          });
          
          // Add active class to clicked tab
          this.classList.add('tab-active', 'text-blue-600');
          this.classList.remove('text-gray-600');
          
          // Scroll to section
          const targetId = this.getAttribute('href');
          const targetSection = document.querySelector(targetId);
          if (targetSection) {
            window.scrollTo({
              top: targetSection.offsetTop - 100,
              behavior: 'smooth'
            });
          }
        });
      });
    });
  </script>
</body>
</html>