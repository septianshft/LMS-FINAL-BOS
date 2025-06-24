<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizOption extends Model
{
    use HasFactory;

    protected $fillable = ['quiz_question_id', 'option_text', 'is_correct']; // Changed 'text' back to 'option_text'

    public function question()
    {
        // Ensure the foreign key in 'quiz_options' table is 'quiz_question_id'
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id'); 
    }
}

