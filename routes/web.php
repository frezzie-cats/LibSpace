<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\FacilityController as StaffFacilityController;
use App\Http\Controllers\Staff\BookingManagementController;
use App\Http\Controllers\Staff\FeedbackController as StaffFeedbackController;
use App\Http\Controllers\Student\FacilityController as StudentFacilityController;
use App\Http\Controllers\Student\BookingController;
use App\Http\Controllers\Student\FeedbackController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes (Authentication)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Determine where to redirect based on authentication and role
    if (Auth::check()) {
        if (Auth::user()->role === User::ROLE_STAFF) {
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
    if (Auth::user()->role === User::ROLE_STAFF) {
        return redirect()->route('staff.dashboard');
    }
    // Student redirect
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

Route::middleware(['auth', 'role:' . User::ROLE_STAFF])->prefix('staff')->name('staff.')->group(function () {
    
    // Staff Dashboard
    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');

    // Facility Management CRUD
    Route::resource('facilities', StaffFacilityController::class)->except(['show']);
    
    // ----------------------------------------------------
    // Booking Overview and Management
    // ----------------------------------------------------
    Route::get('bookings', [BookingManagementController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [BookingManagementController::class, 'show'])->name('bookings.show');

    // ----------------------------------------------------
    // Facility Feedback Review and Management (NEW)
    // ----------------------------------------------------
    Route::prefix('feedbacks')->name('feedbacks.')->controller(StaffFeedbackController::class)->group(function () {
        // GET /staff/feedbacks (Dashboard view of all feedback)
        Route::get('/', 'index')->name('index');
        
        // PATCH /staff/feedbacks/{feedback} (Update status via dropdown)
        Route::patch('/{feedback}', 'updateStatus')->name('update_status');
    });
});


/*
|--------------------------------------------------------------------------
| Student Routes (Role: student)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:' . User::ROLE_STUDENT])->prefix('student')->name('student.')->group(function () {

    // 1. Facility Viewing (Equivalent to the student's main dashboard)
    Route::get('facilities', [StudentFacilityController::class, 'index'])->name('facilities.index');
    Route::get('facilities/{facility}', [StudentFacilityController::class, 'show'])->name('facilities.show');

    // 2. Booking Management (CRUD)
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::delete('bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // 3. Student Feedback Submission
    Route::get('feedbacks', [FeedbackController::class, 'index'])->name('feedbacks.index');
    Route::post('feedbacks', [FeedbackController::class, 'store'])->name('feedbacks.store');

});

require __DIR__.'/auth.php';