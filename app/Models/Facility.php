<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * We must explicitly add 'type' and 'status' to the fillable array
     * to allow them to be saved when using Facility::create($request->all()).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'capacity',
        'type',         // <-- FIX 1: Added 'type' to resolve the NOT NULL error
        'status',       // <-- FIX 2: Added 'status' to match the data sent from the form
        'is_available', // Kept for legacy/other functionality, but 'status' is now primary
        'opening_time',
        'closing_time',
    ];

    /**
     * Get the bookings associated with the facility.
     */
    public function bookings()
    {
        // A Facility can have many Bookings
        return $this->hasMany(Booking::class);
    }
}