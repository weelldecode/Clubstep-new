<?php

namespace App\Notifications;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VerifiedCreatorPublishedCollection extends Notification
{
    use Queueable;

    public function __construct(
        public Collection $collection,
        public User $creator,
    ) {}

    public function via(object $notifiable): array
    {
        return ["database"];
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => "{$this->creator->name} lançou uma nova coleção: {$this->collection->name}",
            "collection_id" => $this->collection->id,
            "collection_slug" => $this->collection->slug,
            "creator_id" => $this->creator->id,
            "creator_name" => $this->creator->name,
            "url" => route("collection.show", $this->collection),
        ];
    }
}

