<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = ['final_quiz_id', 'user_id', 'score', 'is_passed'];

    public function quiz()
    {
        return $this->belongsTo(FinalQuiz::class, 'final_quiz_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
