<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable("translations")) {
            Schema::create("translations", function (Blueprint $table) {
                $table->id();
                $table->string("key");
                $table->string("locale", 10)->default("pt_BR");
                $table->longText("value");
                $table->boolean("is_active")->default(true);
                $table->timestamps();

                $table->unique(["key", "locale"]);
                $table->index(["locale", "is_active"]);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable("translations")) {
            Schema::drop("translations");
        }
    }
};
