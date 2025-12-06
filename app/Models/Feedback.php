<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback'; 

    protected $fillable = [
        'user_id',
        'subject',
        'rating',
        'message',
        'status',
    ];

    protected $attributes = [
        'status' => 'new',
    ];

    /**
     * Get the user (student) that submitted the feedback.
     * We use a generic 'user' relationship since 'user_id' links to the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the student (alias for user) that submitted the feedback.
     * This relationship exists solely to support the code "Feedback::with('student')"
     * used in the Staff\FeedbackController for better readability.
     */
    public function student()
    {
        return $this->user();
    }
}