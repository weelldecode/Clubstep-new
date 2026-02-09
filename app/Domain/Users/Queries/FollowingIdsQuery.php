<?php

namespace App\Domain\Users\Queries;

use App\Models\User;

final class FollowingIdsQuery
{
    public function run(User $user): array
    {
        // Evita pluck com join estranho dependendo do relacionamento
        return $user
            ->following()
            ->select("users.id")
            ->pluck("users.id")
            ->values()
            ->all();
    }
}
