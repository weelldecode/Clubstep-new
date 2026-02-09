<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Subscription extends Model
{
    protected static ?string $resolvedTableName = null;

    protected $fillable = [
        "user_id",
        "plan_id",
        "status",
        "started_at",
        "expires_at",
    ];

    protected $casts = [
        "started_at" => "datetime",
        "expires_at" => "datetime",
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class, "plan_id");
    }

    public function getTable()
    {
        if (self::$resolvedTableName !== null) {
            return self::$resolvedTableName;
        }

        if (Schema::hasTable("subscriptions")) {
            self::$resolvedTableName = "subscriptions";
            return self::$resolvedTableName;
        }

        if (Schema::hasTable("subscription")) {
            self::$resolvedTableName = "subscription";
            return self::$resolvedTableName;
        }

        self::$resolvedTableName = parent::getTable();
        return self::$resolvedTableName;
    }
}
