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
        Schema::create("downloads", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table
                ->foreignId("collection_id")
                ->constrained()
                ->cascadeOnDelete();
            $table->string("status")->default("pending"); // pending, processing, ready, failed
            $table->string("file_path")->nullable(); // caminho do ZIP gerado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("downloads");
    }
};
