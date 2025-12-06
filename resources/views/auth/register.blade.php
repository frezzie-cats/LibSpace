<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register for LibSpace</title>
<!-- Load Tailwind CSS from CDN for instant styling -->
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* Ensuring font consistency across the application /
body {
font-family: 'Inter', sans-serif;
background-color: #0d0d0d;
}
/ Custom class for a softer, more inviting shadow */
.login-card-shadow {
box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3), 0 5px 15px rgba(0, 0, 0, 0.2);
}
</style>
</head>
<body>

<!-- Main Container: Full viewport height and width. Added py-12 to ensure vertical padding 
     and prevent the form from stretching to the edges, while maintaining centering. -->
<div class="relative min-h-screen w-full flex flex-col items-center justify-center py-12">
    
    <!-- Background Image Element using Blade asset() helper -->
    <div class="absolute inset-0 bg-cover bg-center" 
         style="background-image: url('/assets/Inside the library 1.jpg');">
    </div>

    <!-- Dark, Slightly Blurred Overlay (Ensures text readability) -->
    <div class="absolute inset-0 bg-gray-900 bg-opacity-80 backdrop-blur-sm"></div>

    <!-- ENHANCED Registration Card Container -->
    <div class="relative z-10 w-full sm:max-w-md px-8 py-10 bg-white login-card-shadow overflow-hidden rounded-3xl border-t-8 border-emerald-600 transition duration-500">
        
        <h2 class="text-4xl font-extrabold text-gray-900 text-center mb-2 tracking-tight">
            Create Account
        </h2>
        <p class="text-center text-gray-500 mb-8 text-lg font-medium">
            Join <span class="text-teal-600">LibSpace</span> today
        </p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block font-semibold text-sm text-gray-700 mb-1">Name</label>
                <input id="name" class="border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-inner p-3 block w-full transition duration-150" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Enter your name" />
                
                @error('name')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <label for="email" class="block font-semibold text-sm text-gray-700 mb-1">Email Address</label>
                <input id="email" class="border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-inner p-3 block w-full transition duration-150" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@university.edu" />
                
                @error('email')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block font-semibold text-sm text-gray-700 mb-1">Password</label>
                <input id="password" class="border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-inner p-3 block w-full transition duration-150"
                        type="password"
                        name="password"
                        required autocomplete="new-password" placeholder="••••••••" />
                
                @error('password')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <label for="password_confirmation" class="block font-semibold text-sm text-gray-700 mb-1">Confirm Password</label>
                <input id="password_confirmation" class="border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-inner p-3 block w-full transition duration-150"
                        type="password"
                        name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                
                @error('password_confirmation')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="mt-8">
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-emerald-600 border border-transparent rounded-xl font-bold text-base text-white uppercase tracking-wider shadow-lg hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Register
                </button>
            </div>

            <!-- Link to Login -->
            <div class="mt-6 text-center text-sm text-gray-600">
                Already registered? 
                <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-800 font-bold underline transition duration-150">Log in</a>
            </div>
        </form>
    </div>
</div>


</body>
</html>