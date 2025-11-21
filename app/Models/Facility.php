<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These must match the columns in your 'facilities' migration.
     * Note: 'id' and 'timestamps' are protected by default.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'capacity',
        'status', // Allows staff to set the initial status
    ];
}