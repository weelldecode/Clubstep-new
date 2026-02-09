<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable("carts")) {
            Schema::create("carts", function (Blueprint $table) {
                $table->id();
                $table->foreignId("user_id")->constrained()->onDelete("cascade");
                $table->string("status")->default("active");
                $table->timestamps();

                $table->index(["user_id", "status"]);
            });
        }

        if (!Schema::hasTable("cart_items")) {
            Schema::create("cart_items", function (Blueprint $table) {
                $table->id();
                $table->foreignId("cart_id")->constrained("carts")->onDelete("cascade");
                $table->foreignId("item_id")->constrained("items")->onDelete("cascade");
                $table->unsignedInteger("quantity")->default(1);
                $table->decimal("price", 10, 2)->default(0);
                $table->timestamps();

                $table->unique(["cart_id", "item_id"]);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable("cart_items")) {
            Schema::drop("cart_items");
        }

        if (Schema::hasTable("carts")) {
            Schema::drop("carts");
        }
    }
};
