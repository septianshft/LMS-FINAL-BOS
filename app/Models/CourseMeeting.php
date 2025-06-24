<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'start_datetime',
        'end_datetime',
        'location',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
