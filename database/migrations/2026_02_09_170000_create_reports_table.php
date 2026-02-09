<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable("reports")) {
            Schema::create("reports", function (Blueprint $table) {
                $table->id();
                $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();
                $table->foreignId("item_id")->constrained("items")->onDelete("cascade");
                $table->string("reason", 100);
                $table->text("message")->nullable();
                $table->string("status")->default("open");
                $table->timestamps();

                $table->index(["status", "created_at"]);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable("reports")) {
            Schema::drop("reports");
        }
    }
};
