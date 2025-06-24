<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'passing_score',
        'is_hidden_from_trainee', // Add this line
    ];

    protected $casts = [
        'is_hidden_from_trainee' => 'boolean', // Add this line for proper type casting
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class); // Ensure this is QuizQuestion
    }

    // If you have a scope for active/visible quizzes (optional but good practice)
    public function scopeVisibleToTrainees($query)
    {
        return $query->where('is_hidden_from_trainee', false);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
