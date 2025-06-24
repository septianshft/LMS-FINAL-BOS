<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['final_quiz_id', 'question']; // Changed 'text' back to 'question'

    public function finalQuiz()
    {
        return $this->belongsTo(FinalQuiz::class); 
    }

    public function options()
    {
        return $this->hasMany(QuizOption::class);
    }
}