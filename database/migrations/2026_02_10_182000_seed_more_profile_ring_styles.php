<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $styles = [
            [
                "key" => "fire",
                "name" => "Fire",
                "gradient" =>
                    "conic-gradient(from 160deg, #f97316, #ef4444, #f59e0b, #f97316)",
                "border" => "rgba(255,255,255,0.18)",
                "speed" => "7s",
            ],
            [
                "key" => "ice",
                "name" => "Ice",
                "gradient" =>
                    "conic-gradient(from 120deg, #38bdf8, #0ea5e9, #a5f3fc, #38bdf8)",
                "border" => "rgba(255,255,255,0.28)",
                "speed" => "9s",
            ],
            [
                "key" => "galaxy",
                "name" => "Galaxy",
                "gradient" =>
                    "conic-gradient(from 180deg, #6366f1, #a855f7, #ec4899, #6366f1)",
                "border" => "rgba(255,255,255,0.2)",
                "speed" => "11s",
            ],
        ];

        foreach ($styles as $style) {
            DB::table("profile_ring_styles")->updateOrInsert(
                ["key" => $style["key"]],
                array_merge($style, [
                    "updated_at" => now(),
                    "created_at" => now(),
                ]),
            );
        }
    }

    public function down(): void
    {
        DB::table("profile_ring_styles")
            ->whereIn("key", ["fire", "ice", "galaxy"])
            ->delete();
    }
};
