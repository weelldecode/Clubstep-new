<?php

namespace App\Domain\Downloads\Actions;

use App\Jobs\ProcessDownload;
use App\Models\Collection;
use App\Models\Download;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class StartCollectionDownload
{
    public function run(User $user, Collection $collection): array
    {
        if (!Gate::forUser($user)->allows("download", $collection)) {
            return [
                "ok" => false,
                "message" => "Você não tem permissão para baixar esta coleção.",
            ];
        }
        if (
            !method_exists($user, "hasActiveSubscription") ||
            !$user->hasActiveSubscription()
        ) {
            return [
                "ok" => false,
                "message" =>
                    "Você precisa de uma assinatura ativa para baixar coleções.",
            ];
        }

        $existing = Download::query()
            ->where("user_id", $user->id)
            ->where("collection_id", $collection->id)
            ->where("status", "ready")
            ->first();

        if ($existing) {
            return [
                "ok" => true,
                "message" =>
                    "Você já tem este download disponível na sua página de downloads.",
            ];
        }

        $download = Download::create([
            "user_id" => $user->id,
            "collection_id" => $collection->id,
            "status" => "pending",
        ]);

        ProcessDownload::dispatch($download);

        return [
            "ok" => true,
            "message" =>
                "Seu download foi iniciado! Vá até a página de downloads para acompanhar.",
        ];
    }
}
