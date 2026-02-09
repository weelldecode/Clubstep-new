<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = Schema::hasTable("payments") ? "payments" : (Schema::hasTable("payment") ? "payment" : null);

        if ($table && !Schema::hasColumn($table, "order_id")) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedBigInteger("order_id")->nullable()->after("subscription_id");
                $table->index("order_id");
            });
        }
    }

    public function down(): void
    {
        $table = Schema::hasTable("payments") ? "payments" : (Schema::hasTable("payment") ? "payment" : null);

        if ($table && Schema::hasColumn($table, "order_id")) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropIndex(["order_id"]);
                $table->dropColumn("order_id");
            });
        }
    }
};
