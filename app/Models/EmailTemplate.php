<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        "key",
        "name",
        "subject",
        "body_html",
        "variables",
        "is_active",
    ];

    protected $casts = [
        "variables" => "array",
        "is_active" => "boolean",
    ];
}
