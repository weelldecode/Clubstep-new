<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("item_collection", function (Blueprint $table) {
            $table
                ->foreignId("collection_id")
                ->constrained()
                ->onDelete("cascade");
            $table->unsignedBigInteger("parent_id")->after("collection_id");

            $table
                ->foreign("parent_id")
                ->references("id")
                ->on("tags")
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table("item_collection", function (Blueprint $table) {
            $table->dropForeign(["parent_id"]);
            $table->dropColumn("parent_id");
        });
    }
};
