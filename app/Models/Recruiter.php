<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recruiter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recruiters';

    protected $fillable = [
        'user_id',
        'company_name',
        'industry',
        'company_size',
        'website',
        'company_description',
        'phone',
        'address',
        'is_active'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function talentRequests(){
        return $this->hasMany(TalentRequest::class);
    }
}
