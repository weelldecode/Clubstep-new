<?php

namespace App\Domain\Notifications\Actions;

use App\Models\Collection;
use App\Notifications\VerifiedCreatorPublishedCollection;

final class NotifyFollowersOfVerifiedCollectionPublish
{
    public function run(Collection $collection): void
    {
        $creator = $collection->relationLoaded("user")
            ? $collection->user
            : $collection->user()->first();

        if (!$creator) {
            return;
        }

        $isVerifiedCreator =
            ($creator->type ?? null) === "verified" ||
            ($creator->role ?? null) === "verified" ||
            (method_exists($creator, "hasRole") &&
                $creator->hasRole("verified"));

        if (!$isVerifiedCreator) {
            return;
        }

        $followers = $creator->followers()->select("users.id")->get();

        if ($followers->isEmpty()) {
            return;
        }

        foreach ($followers as $follower) {
            $alreadySent = $follower
                ->notifications()
                ->where("type", VerifiedCreatorPublishedCollection::class)
                ->where("data->collection_id", $collection->id)
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $follower->notify(
                new VerifiedCreatorPublishedCollection($collection, $creator),
            );
        }
    }
}
