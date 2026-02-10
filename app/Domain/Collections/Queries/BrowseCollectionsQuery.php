<?php

namespace App\Domain\Collections\Queries;

use App\Domain\Collections\DTOs\CollectionBrowseFilters;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Builder;

final class BrowseCollectionsQuery
{
    public function build(CollectionBrowseFilters $f): Builder
    {
        // whitelist de ordenação (evita SQL injection por sortField)
        $allowedSorts = ["name", "created_at", "updated_at"];
        $sortField = in_array($f->sortField, $allowedSorts, true)
            ? $f->sortField
            : "name";
        $sortDir = $f->sortDirection === "desc" ? "desc" : "asc";

        return Collection::query()
            // Evite carregar itens.tags no index: é pesado demais.
            // Se precisar mostrar contagem/preview, use withCount/limit em relação separada.
            ->with(["categories", "tags", "legacyTags", "user"])
            ->when(
                $f->search !== "",
                fn($q) => $q->where("name", "like", "%{$f->search}%"),
            )
            ->when(!empty($f->collectionTypes), function ($q) use ($f) {
                $q->whereIn("type", $f->collectionTypes);
            })
            ->when(!empty($f->collectionCategoryIds), function ($q) use ($f) {
                $q->where(function ($qq) use ($f) {
                    $qq->whereHas(
                        "categories",
                        fn($q2) => $q2->whereIn(
                            "categories.id",
                            $f->collectionCategoryIds,
                        ),
                    )->orWhereHas(
                        "legacyCategories",
                        fn($q2) => $q2->whereIn(
                            "categories.id",
                            $f->collectionCategoryIds,
                        ),
                    )->orWhereHas(
                        "items.categories",
                        fn($q2) => $q2->whereIn(
                            "categories.id",
                            $f->collectionCategoryIds,
                        ),
                    );
                });
            })
            ->when(!empty($f->collectionTagIds), function ($q) use ($f) {
                $q->where(function ($qq) use ($f) {
                    $qq->whereHas(
                        "tags",
                        fn($q2) => $q2->whereIn("tags.id", $f->collectionTagIds),
                    )->orWhereHas(
                        "legacyTags",
                        fn($q2) => $q2->whereIn("tags.id", $f->collectionTagIds),
                    );
                });
            })
            ->when(!empty($f->itemTagIds), function ($q) use ($f) {
                $q->whereHas(
                    "items.tags",
                    fn($q2) => $q2->whereIn("tags.id", $f->itemTagIds),
                );
            })
            ->when(
                !auth()->check(),
                fn($q) => $q
                    ->where("status", "published")
                    ->whereIn("visibility", ["public", "unlisted"]),
            )
            ->orderBy($sortField, $sortDir);
    }
}
