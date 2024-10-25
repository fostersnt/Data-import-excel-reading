<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'gender',
        'num_of_programs',
        'type',
        'status',
        'district_id',
        'location_id',
        'region_id',
        'category_id',
        'is_special_boarding_catchment_area',
        'is_cluster',
        'track',
        'specialization',
        'is_private'
    ];

    public function programme()
    {
        return $this->belongsToMany(Programme::class, 'school_programmes')->withTimestamps();
    }

    public function curriculum()
    {
        return $this->belongsToMany(Curriculum::class, 'privateschool_curricula', 'school_id', 'curricula_id')->withTimestamps();
    }
}
