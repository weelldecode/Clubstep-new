<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Payment extends Model
{
    protected static ?string $resolvedTableName = null;

    protected $fillable = [
        "subscription_id",
        "order_id",
        "payment_id_mercadopago",
        "amount",
        "status",
        "paid_at",
    ];
    protected $casts = [
        "paid_at" => "datetime",
    ];

    public function plan()
    {
        // atalho: pega direto o plano pela assinatura
        return $this->subscription?->plan;
    }
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getTable()
    {
        if (self::$resolvedTableName !== null) {
            return self::$resolvedTableName;
        }

        if (Schema::hasTable("payments")) {
            self::$resolvedTableName = "payments";
            return self::$resolvedTableName;
        }

        if (Schema::hasTable("payment")) {
            self::$resolvedTableName = "payment";
            return self::$resolvedTableName;
        }

        self::$resolvedTableName = parent::getTable();
        return self::$resolvedTableName;
    }
}
