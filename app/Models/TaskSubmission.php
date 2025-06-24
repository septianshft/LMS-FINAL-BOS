<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_task_id',
        'user_id',
        'file_path',
        'answer',
        'grade',
    ];

    protected $casts = [
        'grade' => 'integer',
    ];

    public function task()
    {
        return $this->belongsTo(ModuleTask::class, 'module_task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
