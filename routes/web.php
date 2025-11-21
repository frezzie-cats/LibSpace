<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\FacilityController as StaffFacilityController;
use App\Http\Controllers\Student\FacilityController as StudentFacilityController; // NEW IMPORT
use App\Http\Controllers\Student\BookingController; // NEW IMPORT
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <-- ADDED to fix Undefined method 'user' and 'id'

/*
|--------------------------------------------------------------------------
| Public Routes (Authentication)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Determine where to redirect based on authentication and role
    if (Auth::check()) {
        if (Auth::user()->role === 'staff') {
            return redirect()->route('staff.dashboard');
        } else {
            // Default student redirect
            return redirect()->route('student.facilities.index');
        }
    }
    return view('welcome');
});

Route::get('/dashboard', function () {
    // Dashboard redirect based on role
    if (Auth::user()->role === 'staff') {
        return redirect()->route('staff.dashboard');
    }
    // Student redirect (or standard Breeze dashboard, though we'll use student.facilities.index)
    return redirect()->route('student.facilities.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Staff Routes (Role: staff)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    // Staff Dashboard
    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');

    // Facility Management CRUD
    Route::resource('facilities', StaffFacilityController::class)->except(['show']);
});


/*
|--------------------------------------------------------------------------
| Student Routes (Role: student)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {

    // 1. Facility Viewing (Equivalent to the student's main dashboard)
    // List all available facilities for booking
    Route::get('facilities', [StudentFacilityController::class, 'index'])->name('facilities.index');
    
    // View a single facility to see available time slots and capacity
    Route::get('facilities/{facility}', [StudentFacilityController::class, 'show'])->name('facilities.show');


    // 2. Booking Management (CRUD)
    // List the student's personal bookings
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    
    // Store a new booking (POST request from facilities.show page)
    Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');

    // Cancel a booking (DELETE request)
    Route::delete('bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

});

require __DIR__.'/auth.php';