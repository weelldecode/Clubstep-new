<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("profile_ring_styles", function (Blueprint $table) {
            $table->id();
            $table->string("key")->unique();
            $table->string("name");
            $table->string("gradient");
            $table->string("border")->nullable();
            $table->string("speed")->default("8s");
            $table->timestamps();
        });

        DB::table("profile_ring_styles")->insert([
            [
                "key" => "aurora",
                "name" => "Aurora",
                "gradient" =>
                    "conic-gradient(from 120deg, #22d3ee, #6366f1, #f97316, #22d3ee)",
                "border" => "rgba(255,255,255,0.25)",
                "speed" => "8s",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "key" => "neon",
                "name" => "Neon",
                "gradient" =>
                    "conic-gradient(from 90deg, #ec4899, #8b5cf6, #22d3ee, #ec4899)",
                "border" => "rgba(255,255,255,0.18)",
                "speed" => "6s",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "key" => "sunset",
                "name" => "Sunset",
                "gradient" =>
                    "conic-gradient(from 140deg, #fb7185, #f59e0b, #f97316, #fb7185)",
                "border" => "rgba(255,255,255,0.22)",
                "speed" => "10s",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "key" => "fire",
                "name" => "Fire",
                "gradient" =>
                    "conic-gradient(from 160deg, #f97316, #ef4444, #f59e0b, #f97316)",
                "border" => "rgba(255,255,255,0.18)",
                "speed" => "7s",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "key" => "ice",
                "name" => "Ice",
                "gradient" =>
                    "conic-gradient(from 120deg, #38bdf8, #0ea5e9, #a5f3fc, #38bdf8)",
                "border" => "rgba(255,255,255,0.28)",
                "speed" => "9s",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "key" => "galaxy",
                "name" => "Galaxy",
                "gradient" =>
                    "conic-gradient(from 180deg, #6366f1, #a855f7, #ec4899, #6366f1)",
                "border" => "rgba(255,255,255,0.2)",
                "speed" => "11s",
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists("profile_ring_styles");
    }
};
