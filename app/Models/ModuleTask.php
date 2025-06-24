<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_module_id',
        'name',
        'description',
        'order',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }
}
