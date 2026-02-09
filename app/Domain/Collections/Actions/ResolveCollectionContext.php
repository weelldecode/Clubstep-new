<?php

namespace App\Domain\Collections\Actions;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Tag;

final class ResolveCollectionContext
{
    public function run(?string $slug): array
    {
        if (!$slug) {
            return ["type" => "none", "model" => null];
        }

        if ($collection = Collection::where("slug", $slug)->first()) {
            return ["type" => "collection", "model" => $collection];
        }

        if ($category = Category::where("slug", $slug)->first()) {
            return ["type" => "category", "model" => $category];
        }

        if ($tag = Tag::where("slug", $slug)->first()) {
            return ["type" => "tag", "model" => $tag];
        }

        return ["type" => "none", "model" => null];
    }
}
