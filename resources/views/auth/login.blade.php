<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log in to LibSpace</title>
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

<!-- Main Container: Full viewport height and width with background image -->
<div class="relative min-h-screen w-full flex flex-col items-center justify-center">
    
    <!-- Background Image Element using Blade asset() helper -->
    <div class="absolute inset-0 bg-cover bg-center" 
         style="background-image: url('/assets/Inside the library 1.jpg');">
    </div>

    <!-- Dark, Slightly Blurred Overlay (Ensures text readability) -->
    <div class="absolute inset-0 bg-gray-900 bg-opacity-80 backdrop-blur-sm"></div>

    <!-- ENHANCED Login Card Container -->
    <div class="relative z-10 w-full sm:max-w-md px-8 py-10 bg-white login-card-shadow overflow-hidden rounded-3xl border-t-8 border-emerald-600">
        
        <h2 class="text-4xl font-extrabold text-gray-900 text-center mb-2 tracking-tight">
            Sign In
        </h2>
        <p class="text-center text-gray-500 mb-8 text-lg font-medium">
            to <span class="text-teal-600">LibSpace</span>
        </p>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="mb-4 p-3 font-medium text-sm bg-green-100 text-green-700 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mt-4">
                <label for="email" class="block font-semibold text-sm text-gray-700 mb-1">Email Address</label>
                
                <!-- Enhanced Input Styling -->
                <input id="email" class="border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-inner p-3 block w-full transition duration-150" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@university.edu" />
                
                @error('email')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mt-6">
                <label for="password" class="block font-semibold text-sm text-gray-700 mb-1">Password</label>
                
                <!-- Enhanced Input Styling -->
                <input id="password" class="border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-inner p-3 block w-full transition duration-150"
                        type="password"
                        name="password"
                        required autocomplete="current-password" placeholder="••••••••" />
                
                @error('password')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me and Forgot Password -->
            <div class="flex items-center justify-between mt-6">
                <label for="remember_me" class="inline-flex items-center">
                    {{-- Checkbox uses green accent color --}}
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-500 hover:text-emerald-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150" href="{{ route('password.request') }}">
                        Forgot Password?
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="mt-8">
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-emerald-600 border border-transparent rounded-xl font-bold text-base text-white uppercase tracking-wider shadow-lg hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 transform hover:scale-[1.005]">
                    Log in
                </button>
            </div>

            <!-- Link to Register -->
            <div class="mt-6 text-center text-sm text-gray-600">
                Need an account? 
                <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-800 font-bold underline transition duration-150">Create one now</a>
            </div>
        </form>
    </div>
</div>


</body>
</html>