<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("collection_tag", function (Blueprint $table) {
            $table->id();
            $table->foreignId("collection_id")->constrained()->onDelete("cascade");
            $table->foreignId("tag_id")->constrained()->onDelete("cascade");
            $table->timestamps();

            $table->unique(["collection_id", "tag_id"]);
        });

        // Backfill opcional de dados legados (se existir).
        if (Schema::hasTable("item_collection")) {
            $rows = DB::table("item_collection")
                ->select(["collection_id", "parent_id as tag_id"])
                ->where("type", "tag")
                ->whereNotNull("collection_id")
                ->whereNotNull("parent_id")
                ->distinct()
                ->get();

            foreach ($rows as $row) {
                DB::table("collection_tag")->updateOrInsert(
                    [
                        "collection_id" => (int) $row->collection_id,
                        "tag_id" => (int) $row->tag_id,
                    ],
                    [
                        "updated_at" => now(),
                        "created_at" => now(),
                    ],
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("collection_tag");
    }
};

