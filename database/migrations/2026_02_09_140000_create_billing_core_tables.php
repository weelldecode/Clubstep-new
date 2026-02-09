<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable("plans")) {
            Schema::create("plans", function (Blueprint $table) {
                $table->id();
                $table->string("name");
                $table->string("slug")->unique();
                $table->text("description")->nullable();
                $table->unsignedInteger("limit_download")->default(0);
                $table->json("features")->nullable();
                $table->decimal("price", 10, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable("subscriptions")) {
            Schema::create("subscriptions", function (Blueprint $table) {
                $table->id();
                $table->foreignId("user_id")->constrained()->onDelete("cascade");
                $table->foreignId("plan_id")->nullable()->constrained("plans")->nullOnDelete();
                $table->string("status")->default("pending");
                $table->timestamp("started_at")->nullable();
                $table->timestamp("expires_at")->nullable();
                $table->boolean("notified_expiring")->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable("payments")) {
            Schema::create("payments", function (Blueprint $table) {
                $table->id();
                // Sem FK aqui para compatibilidade com schemas legados
                // (evita erro 150 em bancos que ja possuem subscriptions com estrutura diferente).
                $table->unsignedBigInteger("subscription_id")->nullable()->index();
                $table->string("payment_id_mercadopago")->nullable()->index();
                $table->decimal("amount", 10, 2)->default(0);
                $table->string("status")->default("pending");
                $table->timestamp("paid_at")->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable("payments")) {
            Schema::drop("payments");
        }

        if (Schema::hasTable("subscriptions")) {
            Schema::drop("subscriptions");
        }

        if (Schema::hasTable("plans")) {
            Schema::drop("plans");
        }
    }
};
