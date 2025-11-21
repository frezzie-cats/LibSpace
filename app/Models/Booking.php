<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facility_id',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        // Note: start_time and end_time are best treated as strings/timestamps 
        // in Laravel to simplify database interactions.
    ];

    /**
     * Get the user (student) who owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the facility that was booked.
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}