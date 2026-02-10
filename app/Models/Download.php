<?php

namespace App\Models;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;
    protected $fillable = ["user_id", "collection_id", "status", "file_path"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
