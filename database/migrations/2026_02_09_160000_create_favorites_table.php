<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable("favorites")) {
            Schema::create("favorites", function (Blueprint $table) {
                $table->id();
                $table->foreignId("user_id")->constrained()->onDelete("cascade");
                $table->foreignId("item_id")->constrained("items")->onDelete("cascade");
                $table->timestamps();

                $table->unique(["user_id", "item_id"]);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable("favorites")) {
            Schema::drop("favorites");
        }
    }
};
