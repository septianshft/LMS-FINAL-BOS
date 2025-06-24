<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\CourseVideo;
use App\Models\CourseMaterial;
use App\Models\ModuleTask;

class CourseModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'order',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function videos()
    {
        return $this->hasMany(CourseVideo::class)->orderBy('order');
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class)->orderBy('order');
    }

    public function tasks()
    {
        return $this->hasMany(ModuleTask::class)->orderBy('order');
    }
}
