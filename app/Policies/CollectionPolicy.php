<?php

namespace App\Policies;

use App\Models\Collection;
use App\Models\User;

class CollectionPolicy
{
    public function view(?User $user, Collection $collection): bool
    {
        // Coleção pública publicada: qualquer um vê
        if (
            $collection->visibility === "public" &&
            $collection->status === "published"
        ) {
            return true;
        }

        // Unlisted publicada: qualquer um com link vê
        if (
            $collection->visibility === "unlisted" &&
            $collection->status === "published"
        ) {
            return true;
        }

        // Privada ou draft: só dono (ou admin)
        if ($user && $user->id === $collection->user_id) {
            return true;
        }

        return $user ? $user->hasRole("admin") : false;
    }

    public function download(User $user, Collection $collection): bool
    {
        // precisa conseguir ver a coleção
        if (!$this->view($user, $collection)) {
            return false;
        }

        // dono sempre pode baixar
        if ($user->id === $collection->user_id) {
            return true;
        }

        // admin pode
        if ($user->hasRole("admin")) {
            return true;
        }

        // assinante pode (sua regra atual)
        return method_exists($user, "hasActiveSubscription") &&
            $user->hasActiveSubscription();
    }

    public function update(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id || $user->hasRole("admin");
    }

    public function delete(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id || $user->hasRole("admin");
    }

    public function manage(User $user): bool
    {
        return $user->hasRole("admin");
    }
}
