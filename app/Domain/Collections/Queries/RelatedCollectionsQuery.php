<?php

namespace App\Domain\Collections\Queries;

use App\Models\Collection;

final class RelatedCollectionsQuery
{
    public function run(Collection $collection, int $limit = 4)
    {
        $categoryIds = $collection->categories->pluck("id");

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return Collection::query()
            ->whereHas(
                "categories",
                fn($q) => $q->whereIn("categories.id", $categoryIds),
            )
            ->whereKeyNot($collection->id)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
