<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['code', 'name'];

    public function school()
    {
        return $this->belongsToMany(School::class, 'school_programmes')->withTimestamps();
    }
}
