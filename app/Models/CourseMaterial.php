<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_module_id',
        'name',
        'file_path',
        'file_type',
        'order',
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }
}
