<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EduManagePro — Login & Register</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    .form-container {
      height: 42rem;
      transition: transform 0.6s ease-in-out;
    }
    .form-panel {
      min-width: 100%;
      transition: opacity 0.4s ease, transform 0.4s ease;
    }
    .slide-left { transform: translateX(-100%); }
    .slide-right { transform: translateX(0%); }

    /* Hidden field animations */
    .hidden-field {
      max-height: 0;
      opacity: 0;
      overflow: hidden;
      transition: all 0.4s ease;
    }
    .hidden-field.show {
      max-height: 80px; /* enough space for input */
      opacity: 1;
    }
  </style>
</head>
<body class="bg-gradient-to-r from-blue-50 to-blue-100 flex items-center justify-center min-h-screen">
  <div class="bg-white shadow-2xl rounded-2xl overflow-hidden w-full max-w-4xl flex flex-col md:flex-row">

    <!-- Branding -->
    <div class="hidden md:flex bg-gradient-to-br from-blue-600 to-blue-700 text-white flex-col items-center justify-center p-10 w-1/2">
      <div class="bg-white text-blue-700 p-4 rounded-xl shadow-lg mb-4">
        <i class="fas fa-graduation-cap text-3xl"></i>
      </div>
      <h1 class="text-3xl font-bold">EduManage<span class="text-yellow-300">Pro</span></h1>
      <p class="mt-2 text-blue-100 text-center">Smart School Management System</p>
    </div>

    <!-- Forms Wrapper -->
    <div class="relative w-full md:w-1/2 overflow-hidden">
      <div id="forms-wrapper" class="flex form-container slide-right">
 <!-- Register Form -->
        <div class="form-panel p-10">
          <h2 class="text-xl font-bold text-gray-800 mb-5">Register</h2>
          <form method="POST" action="{{ route('register') }}" class="space-y-3">
            @csrf
            <div>
              <label class="block text-sm font-medium">Full Name</label>
              <input type="text" name="name" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
              <label class="block text-sm font-medium">Email</label>
              <input type="email" name="email" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <!-- Password with strong validation -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  <!-- Password -->
  <div>
    <label class="block text-sm font-medium">Password</label>
    <div class="relative">
      <input type="password" name="password" id="password" required 
        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none pr-10"
        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
        title="Must be at least 8 characters long, include uppercase, lowercase, a number, and a special character."
      />
      <button type="button" onclick="togglePassword('password')" 
        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
        <i class="fas fa-eye"></i>
      </button>
    </div>
  </div>

  <!-- Confirm Password -->
  <div>
    <label class="block text-sm font-medium">Confirm Password</label>
    <div class="relative">
      <input type="password" name="password_confirmation" id="password_confirmation" required 
        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none pr-10" />
      <button type="button" onclick="togglePassword('password_confirmation')" 
        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
        <i class="fas fa-eye"></i>
      </button>
    </div>
  </div>
</div>


<!-- CNIC fields -->


            <div>
              <label class="block text-sm font-medium">Role</label>
              <select name="role" id="role" required class="w-full  p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">-- choose role --</option>
                <option value="student">Student</option>
                <option value="parent">Parent</option>
                <option value="teacher">Teacher</option>
              </select>
            </div>
            <!-- CNIC fields with reserved space -->
            <div id="cnic-group" class="hidden-field">
  <label class="block text-sm font-small">CNIC</label>
  <input name="cnic" placeholder="34603-5252051-1"
    class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
    pattern="^\d{5}-\d{7}-\d{1}$"
    title="CNIC must be in 12345-1234567-1 format"
  />
</div>

<div id="parent-cnic-group" class="hidden-field">
  <label class="block text-sm font-small">Parent CNIC</label>
  <input name="parent_cnic" placeholder="34603-5252051-1"
    class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
    pattern="^\d{5}-\d{7}-\d{1}$"
    title="CNIC must be in 12345-1234567-1 format"
  />
</div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow">Register</button>
          </form>
          <p class="mt-6 text-sm text-gray-600 text-center">Already have an account? 
            <button onclick="toggleForms()" class="text-blue-600 font-medium hover:underline">Login</button>
          </p>
        </div>
        <!-- Login Form -->
        <div class="form-panel p-10">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">Login</h2>
          <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
              <label class="block text-sm font-medium">Email</label>
              <input type="email" name="email" required class="w-full mt-1 p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
  <label class="block text-sm font-medium">Password</label>
  <div class="relative">
    <input type="password" name="password" id="login_password" required 
      class="w-full mt-1 p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none pr-10" />
    <button type="button" onclick="togglePassword('login_password')" 
      class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
      <i class="fas fa-eye"></i>
    </button>
  </div>
</div>
            <div class="flex items-center justify-between">
              <label class="inline-flex items-center">
                <input type="checkbox" name="remember" class="mr-2"> <span class="text-sm">Remember me</span>
              </label>
              <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow">Login</button>
            </div>
          </form>
          <p class="mt-6 text-sm text-gray-600 text-center">Don’t have an account? 
            <button onclick="toggleForms()" class="text-blue-600 font-medium hover:underline">Register</button>
          </p>
        </div>

       
      </div>
    </div>
  </div>

  <!-- JS -->
  <script>
  const wrapper = document.getElementById("forms-wrapper");
  let showingRegister = true; // we load Register first

  function toggleForms() {
    if (showingRegister) {
      wrapper.classList.add("slide-left");   // show Login
      wrapper.classList.remove("slide-right");
    } else {
      wrapper.classList.remove("slide-left"); // show Register
      wrapper.classList.add("slide-right");
    }
    showingRegister = !showingRegister;
  }
const password = document.querySelector("input[name='password']");
const confirmPassword = document.querySelector("input[name='password_confirmation']");

confirmPassword.addEventListener("input", function () {
  if (confirmPassword.value !== password.value) {
    confirmPassword.setCustomValidity("Passwords do not match");
  } else {
    confirmPassword.setCustomValidity("");
  }
});
function togglePassword(id) {
  const input = document.getElementById(id);
  const icon = input.nextElementSibling.querySelector("i");

  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}
  // CNIC role-based logic with smooth animation
  const roleSelect = document.getElementById("role");
  const cnicGroup = document.getElementById("cnic-group");
  const parentCnicGroup = document.getElementById("parent-cnic-group");
  const cnicInput = cnicGroup.querySelector("input");
  const parentCnicInput = parentCnicGroup.querySelector("input");

  roleSelect.addEventListener("change", function () {
    const role = this.value;

    if (role === "student") {
      cnicGroup.classList.add("show");
      parentCnicGroup.classList.add("show");

      cnicInput.required = true;
      parentCnicInput.required = true;
    } 
    else if (role === "parent") {
      cnicGroup.classList.add("show");
      parentCnicGroup.classList.remove("show");

      cnicInput.required = true;
      parentCnicInput.required = false;
    } 
    else {
      cnicGroup.classList.remove("show");
      parentCnicGroup.classList.remove("show");

      cnicInput.required = false;
      parentCnicInput.required = false;
    }
  });
</script>

</body>
</html>
