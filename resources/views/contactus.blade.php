<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact Us - EduManage Pro</title>
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
    .contact-icon {
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
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
      <h1 class="text-4xl font-bold mb-4">Contact Us</h1>
      <p class="text-xl max-w-3xl mx-auto">We'd love to hear from you. Get in touch with our team for any questions or inquiries.</p>
    </div>
  </section>

  <!-- Contact Methods -->
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-gray-50 p-6 rounded-xl text-center card-hover">
          <div class="contact-icon bg-blue-100 text-blue-600 mx-auto mb-4">
            <i class="fas fa-phone-alt text-xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Phone</h3>
          <p class="text-gray-600 mb-2">Speak with our support team</p>
          <p class="text-blue-600 font-medium">+1 (555) 123-4567</p>
          <p class="text-gray-500 text-sm mt-2">Mon-Fri, 9am-5pm EST</p>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-xl text-center card-hover">
          <div class="contact-icon bg-green-100 text-green-600 mx-auto mb-4">
            <i class="fas fa-envelope text-xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Email</h3>
          <p class="text-gray-600 mb-2">Send us a message anytime</p>
          <p class="text-blue-600 font-medium">info@edumanagepro.com</p>
          <p class="text-gray-500 text-sm mt-2">We typically reply within 24 hours</p>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-xl text-center card-hover">
          <div class="contact-icon bg-purple-100 text-purple-600 mx-auto mb-4">
            <i class="fas fa-comments text-xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Live Chat</h3>
          <p class="text-gray-600 mb-2">Chat with our support team</p>
          <p class="text-blue-600 font-medium">Start Chat</p>
          <p class="text-gray-500 text-sm mt-2">Mon-Fri, 9am-5pm EST</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Form & Info -->
  <section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-12">
        <!-- Contact Form -->
        <div class="lg:w-2/3">
          <div class="bg-white p-8 rounded-xl shadow-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Send Us a Message</h2>
            <form>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                  <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                  <input type="text" id="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                  <input type="email" id="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                  <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                  <input type="tel" id="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label for="subject" class="block text-gray-700 font-medium mb-2">Subject</label>
                  <input type="text" id="subject" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
              </div>
              
              <div class="mb-6">
                <label for="message" class="block text-gray-700 font-medium mb-2">Message</label>
                <textarea id="message" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
              </div>
              
              <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold btn-hover w-full">Send Message</button>
            </form>
          </div>
        </div>
        
        <!-- Contact Info -->
        <div class="lg:w-1/3">
          <div class="bg-white p-8 rounded-xl shadow-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Visit Our Office</h2>
            
            <div class="flex items-start mb-6">
              <div class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-4">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 mb-1">Address</h3>
                <p class="text-gray-600">123 Education Street<br>New York, NY 10001<br>United States</p>
              </div>
            </div>
            
            <div class="flex items-start mb-6">
              <div class="bg-green-100 text-green-600 p-2 rounded-lg mr-4">
                <i class="fas fa-clock"></i>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 mb-1">Business Hours</h3>
                <p class="text-gray-600">Monday - Friday: 9:00 AM - 5:00 PM<br>Saturday: 10:00 AM - 2:00 PM<br>Sunday: Closed</p>
              </div>
            </div>
            
            <div class="flex items-start mb-6">
              <div class="bg-purple-100 text-purple-600 p-2 rounded-lg mr-4">
                <i class="fas fa-share-alt"></i>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 mb-1">Follow Us</h3>
                <div class="flex space-x-4 mt-2">
                  <a href="#" class="text-gray-600 hover:text-blue-600"><i class="fab fa-facebook-f text-xl"></i></a>
                  <a href="#" class="text-gray-600 hover:text-blue-400"><i class="fab fa-twitter text-xl"></i></a>
                  <a href="#" class="text-gray-600 hover:text-purple-600"><i class="fab fa-instagram text-xl"></i></a>
                  <a href="#" class="text-gray-600 hover:text-blue-700"><i class="fab fa-linkedin-in text-xl"></i></a>
                </div>
              </div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg mt-8">
              <h3 class="font-semibold text-gray-800 mb-2">Need Immediate Help?</h3>
              <p class="text-gray-600 text-sm">Check out our <a href="#" class="text-blue-600 hover:underline">FAQ section</a> or <a href="#" class="text-blue-600 hover:underline">knowledge base</a> for quick answers to common questions.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>



  <!-- FAQ Preview -->
  <section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Frequently Asked Questions</h2>
        <p class="text-gray-600 max-w-3xl mx-auto">Quick answers to common questions about EduManagePro</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded-xl shadow-sm">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">How long does it take to implement EduManagePro?</h3>
          <p class="text-gray-600">Implementation typically takes 2-4 weeks, depending on the size of your institution and your specific requirements. Our team will guide you through every step of the process.</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Do you offer training for staff?</h3>
          <p class="text-gray-600">Yes, we provide comprehensive training for administrators, teachers, and staff. This includes live training sessions, video tutorials, and detailed documentation.</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Can I customize the platform for our school?</h3>
          <p class="text-gray-600">Absolutely! EduManagePro is highly customizable. We can tailor the platform to match your specific workflows, terminology, and reporting requirements.</p>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-sm">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">What kind of support do you offer?</h3>
          <p class="text-gray-600">We offer 24/7 technical support via email, phone, and live chat. Our support team is always available to help you resolve any issues quickly.</p>
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