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

            // Constraint to prevent double-booking the SAME facility at the SAME time
            // This is primarily for ensuring unique entries, but logic handles overlaps.
            $table->unique(['facility_id', 'booking_date', 'start_time', 'end_time']);
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