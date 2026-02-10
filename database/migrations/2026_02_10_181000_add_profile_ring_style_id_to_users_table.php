<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table
                ->foreignId("profile_ring_style_id")
                ->nullable()
                ->after("profile_animations_enabled")
                ->constrained("profile_ring_styles")
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropConstrainedForeignId("profile_ring_style_id");
        });
    }
};
