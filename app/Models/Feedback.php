<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    // By default, Laravel looks for the plural form (feedbacks). 
    // We explicitly set the table name to 'feedback' as per the migration.
    protected $table = 'feedback'; 

    protected $fillable = [
        'user_id',
        'subject',
        'rating',
        'message',
    ];

    /**
     * Get the user that submitted the feedback.
     */
    public function user()
    {
        // Assuming the User model is in the default App\Models namespace
        return $this->belongsTo(User::class);
    }
}