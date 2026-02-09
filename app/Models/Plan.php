<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Plan extends Model
{
    protected static ?string $resolvedTableName = null;

    protected $fillable = [
        "name",
        "slug",
        "description",
        "limit_download",
        "features",
        "price",
    ];

    protected $casts = [
        "features" => "array",
    ];

    public function getTable()
    {
        if (self::$resolvedTableName !== null) {
            return self::$resolvedTableName;
        }

        if (Schema::hasTable("plans")) {
            self::$resolvedTableName = "plans";
            return self::$resolvedTableName;
        }

        if (Schema::hasTable("plan")) {
            self::$resolvedTableName = "plan";
            return self::$resolvedTableName;
        }

        self::$resolvedTableName = parent::getTable();
        return self::$resolvedTableName;
    }
}
