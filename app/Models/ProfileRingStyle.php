<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileRingStyle extends Model
{
    protected $fillable = ["key", "name", "gradient", "border", "speed"];
}
