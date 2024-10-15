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
        'category',
        'is_special_boarding_catchment_area',
        'is_cluster',
        'track'
    ];

    public function programme()
    {
        return $this->belongsToMany(Programme::class, 'school_programmes')->withTimestamps();
    }
}
