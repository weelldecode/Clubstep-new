<?php

namespace App\Domain\Taxonomy\Queries;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

final class TaxonomyListsQuery
{
    public function collectionsCategories()
    {
        return Cache::remember(
            "tax:v3:categories:collection",
            now()->addHours(6),
            fn() => Category::where(function ($q) {
                $q->where("type", "collection")->orWhereNull("type");
            })
                ->orderBy("name")
                ->get(),
        );
    }

    public function itemsCategories()
    {
        return Cache::remember(
            "tax:v3:categories:item",
            now()->addHours(6),
            fn() => Category::where("type", "item")->orderBy("name")->get(),
        );
    }

    public function tags()
    {
        return Cache::remember(
            "tax:v3:tags",
            now()->addHours(6),
            fn() => Tag::orderBy("name")->get(),
        );
    }
}
