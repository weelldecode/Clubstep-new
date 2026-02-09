<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable("orders")) {
            Schema::create("orders", function (Blueprint $table) {
                $table->id();
                $table->foreignId("user_id")->constrained()->onDelete("cascade");
                $table->string("status")->default("pending");
                $table->decimal("total_amount", 10, 2)->default(0);
                $table->timestamps();

                $table->index(["user_id", "status"]);
            });
        }

        if (!Schema::hasTable("order_items")) {
            Schema::create("order_items", function (Blueprint $table) {
                $table->id();
                $table->foreignId("order_id")->constrained("orders")->onDelete("cascade");
                $table->foreignId("item_id")->constrained("items")->onDelete("cascade");
                $table->unsignedInteger("quantity")->default(1);
                $table->decimal("price", 10, 2)->default(0);
                $table->decimal("total", 10, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable("order_items")) {
            Schema::drop("order_items");
        }

        if (Schema::hasTable("orders")) {
            Schema::drop("orders");
        }
    }
};
