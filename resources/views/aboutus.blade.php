<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us - EduManage Pro</title>
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
    .team-member:hover img {
      transform: scale(1.05);
    }
    .team-member img {
      transition: transform 0.3s ease;
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
      <h1 class="text-4xl font-bold mb-4">About EduManagePro</h1>
      <p class="text-xl max-w-3xl mx-auto">Learn about our mission, values, and the team behind the leading school management system</p>
    </div>
  </section>

  <!-- Our Story -->
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row items-center gap-12">
        <div class="lg:w-1/2">
          <h2 class="text-3xl font-bold text-gray-800 mb-6">Our Story</h2>
          <p class="text-gray-600 mb-4">EduManagePro was founded in 2015 by a team of educators and technology experts who recognized the need for a comprehensive, user-friendly school management system.</p>
          <p class="text-gray-600 mb-4">Frustrated by the disjointed and outdated systems available, we set out to create a platform that would streamline operations, enhance communication, and ultimately improve educational outcomes.</p>
          <p class="text-gray-600">Today, we serve over 250 schools and educational institutions worldwide, with a team dedicated to continuous innovation and customer success.</p>
        </div>
        <div class="lg:w-1/2">
          <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1000&q=80" alt="Our team" class="rounded-xl shadow-lg">
        </div>
      </div>
    </div>
  </section>

  <!-- Mission & Vision -->
  <section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <div class="bg-white p-8 rounded-xl shadow-md card-hover">
          <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center mb-6 mx-auto">
            <i class="fas fa-bullseye text-2xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">Our Mission</h3>
          <p class="text-gray-600 text-center">To empower educational institutions with innovative technology solutions that streamline operations, enhance learning experiences, and foster communication between all stakeholders.</p>
        </div>
        
        <div class="bg-white p-8 rounded-xl shadow-md card-hover">
          <div class="bg-green-100 text-green-600 w-16 h-16 rounded-full flex items-center justify-center mb-6 mx-auto">
            <i class="fas fa-eye text-2xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">Our Vision</h3>
          <p class="text-gray-600 text-center">To become the global standard for educational management systems, transforming how schools operate and creating more effective, connected educational communities worldwide.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Values -->
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Our Values</h2>
        <p class="text-gray-600 max-w-3xl mx-auto">These core principles guide everything we do at EduManagePro</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-gray-50 p-6 rounded-xl text-center card-hover">
          <div class="bg-blue-100 text-blue-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 mx-auto">
            <i class="fas fa-lightbulb text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Innovation</h3>
          <p class="text-gray-600">We continuously seek new ways to improve our platform and address the evolving needs of educators.</p>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-xl text-center card-hover">
          <div class="bg-green-100 text-green-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 mx-auto">
            <i class="fas fa-hand-holding-heart text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Integrity</h3>
          <p class="text-gray-600">We operate with honesty and transparency, building trust with our customers and partners.</p>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-xl text-center card-hover">
          <div class="bg-purple-100 text-purple-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 mx-auto">
            <i class="fas fa-users text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Collaboration</h3>
          <p class="text-gray-600">We believe in the power of working together—with our team, customers, and partners.</p>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-xl text-center card-hover">
          <div class="bg-yellow-100 text-yellow-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4 mx-auto">
            <i class="fas fa-star text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Excellence</h3>
          <p class="text-gray-600">We strive for excellence in everything we do, from product development to customer support.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Team -->
  <section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Meet Our Leadership Team</h2>
        <p class="text-gray-600 max-w-3xl mx-auto">The passionate professionals driving innovation at EduManagePro</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-white p-6 rounded-xl text-center team-member card-hover">
          <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=400&q=80" alt="Team member" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
          <h3 class="text-xl font-semibold text-gray-800 mb-1">Sarah Johnson</h3>
          <p class="text-blue-600 font-medium mb-2">CEO & Founder</p>
          <p class="text-gray-600">Former school administrator with 15+ years of experience in education technology.</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl text-center team-member card-hover">
          <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=400&q=80" alt="Team member" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
          <h3 class="text-xl font-semibold text-gray-800 mb-1">Michael Chen</h3>
          <p class="text-blue-600 font-medium mb-2">CTO</p>
          <p class="text-gray-600">Software engineer with expertise in building scalable educational platforms.</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl text-center team-member card-hover">
          <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=400&q=80" alt="Team member" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
          <h3 class="text-xl font-semibold text-gray-800 mb-1">Emily Rodriguez</h3>
          <p class="text-blue-600 font-medium mb-2">Head of Product</p>
          <p class="text-gray-600">Product manager focused on creating intuitive user experiences for educators.</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl text-center team-member card-hover">
          <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=400&q=80" alt="Team member" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
          <h3 class="text-xl font-semibold text-gray-800 mb-1">David Kim</h3>
          <p class="text-blue-600 font-medium mb-2">Head of Customer Success</p>
          <p class="text-gray-600">Dedicated to ensuring our customers get the most value from our platform.</p>
        </div>
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
</body>
</html>