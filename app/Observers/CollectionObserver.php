<?php

namespace App\Observers;

use App\Domain\Notifications\Actions\NotifyFollowersOfVerifiedCollectionPublish;
use App\Models\Collection;

class CollectionObserver
{
    public function created(Collection $collection): void
    {
        if (!$this->isPublished($collection)) {
            return;
        }

        app(NotifyFollowersOfVerifiedCollectionPublish::class)->run($collection);
    }

    public function updated(Collection $collection): void
    {
        // So notifica quando houve transicao para "published".
        if (!$collection->wasChanged("status") || !$this->isPublished($collection)) {
            return;
        }

        app(NotifyFollowersOfVerifiedCollectionPublish::class)->run($collection);
    }

    private function isPublished(Collection $collection): bool
    {
        $status = $collection->status;

        if ($status instanceof \BackedEnum) {
            $status = $status->value;
        }

        return (string) $status === "published";
    }
}

