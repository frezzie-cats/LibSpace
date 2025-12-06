<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to LibSpace</title>
    <!-- Load Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /*
        Ensuring font consistency across the application.
        The 'Inter' font is used here, matching the clean style in your other views.
        */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0d0d0d; /* Fallback dark background */
        }
    </style>
</head>
<body>

    <!-- Main Container: Full viewport height and width. -->
    <div class="relative h-screen w-full flex flex-col items-center justify-center">
        
        <!-- Background Image with Cover Effect (Library) -->
        <div class="absolute inset-0 bg-cover bg-center" 
             style="background-image: url('/assets/Inside the library 1.jpg');">
        </div>

        <!-- Dark, Slightly Blurred Overlay (Ensures text readability) -->
        <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>

        <!-- Header: Authentication Links (Top Right) -->
        <header class="absolute top-0 right-0 p-6 z-20 space-x-4">
            <a href="/login" class="text-white text-lg font-semibold px-3 py-2 rounded-lg hover:bg-white hover:text-gray-900 transition duration-200">
                Log in
            </a>
            <!-- Register button with prominent emerald green styling -->
            <a href="/register" class="inline-block px-5 py-2 bg-emerald-600 text-white text-lg font-bold rounded-xl shadow-lg hover:bg-emerald-700 transition duration-300 transform hover:scale-105 active:scale-95">
                Register
            </a>
        </header>

        <!-- Centered Welcome Content -->
        <div class="relative z-10 p-8 text-center max-w-2xl mx-auto rounded-xl">
            
            <!-- Main Title with Teal Accent (Using font-bold as previously agreed for less thickness) -->
            <h1 class="text-white text-6xl sm:text-8xl font-bold tracking-tight mb-6">
                Welcome to <span class="text-teal-400">LibSpace</span>
            </h1>
            
            <!-- Subtitle with a lighter green accent (Using font-medium as previously agreed for better readability) -->
            <p class="text-emerald-200 text-xl sm:text-3xl mb-12 font-medium italic">
                Your portal to a better library experience.
            </p>
        </div>
    </div>

</body>
</html>