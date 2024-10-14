<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class school_pivot_programme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['school_id', 'programme_id'];
}
