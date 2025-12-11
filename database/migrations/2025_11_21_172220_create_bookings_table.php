<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            // The student who made the booking
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            // The facility being booked
            $table->foreignId('facility_id')->constrained()->onDelete('cascade'); 

            // Booking details
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            
            // Status: confirmed (default), pending (optional), cancelled
            $table->string('status')->default('confirmed');

            // Optional: for future feature - purpose of booking
            $table->text('notes')->nullable(); 

            $table->timestamps();

            // REMOVED: The unique constraint was removed because it overrode the application's capacity logic.
            // Multiple users must be able to book the same facility at the same time if capacity > 1.
            // Capacity is now enforced entirely within the BookingController store method.
            
            // NOTE: You can, however, add a unique index to prevent a single user from booking the same slot twice
            $table->unique(['user_id', 'booking_date', 'start_time', 'end_time'], 'user_unique_booking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};