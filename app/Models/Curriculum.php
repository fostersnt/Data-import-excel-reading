<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curriculum extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description'];

    public function school()
    {
        return $this->belongsToMany(School::class, 'privateschool_curricula', 'curricula_id', 'school_id')->withTimestamps();
    }
}
