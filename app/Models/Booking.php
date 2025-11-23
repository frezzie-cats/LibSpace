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
        'start_time', // Time stored as 'H:i:s' string
        'end_time',   // Time stored as 'H:i:s' string
        'notes',
        'status',
    ];

    /**
     * Prevent Laravel from automatically casting time columns to Carbon objects.
     * We need them as clean strings ('17:00:00') for manual concatenation.
     */
    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'string', // Ensure this is treated as a simple string
        'end_time' => 'string',   // Ensure this is treated as a simple string
    ];

    /**
     * Relationship to the user who made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to the facility being booked.
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}