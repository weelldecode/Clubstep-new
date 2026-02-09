<?php

namespace App\Domain\Collections\Queries;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Builder;

final class CollectionItemsQuery
{
    public function build(
        Collection $collection,
        string $search,
        array $itemCategoryIds,
        string $sortField,
        string $sortDirection,
    ): Builder {
        $allowedSorts = ["name", "created_at", "updated_at"];
        $sortField = in_array($sortField, $allowedSorts, true)
            ? $sortField
            : "name";
        $sortDir = $sortDirection === "desc" ? "desc" : "asc";

        // 👇 Aqui é o ponto: pega o Builder real por trás da relação
        $query = $collection->items()->getQuery();

        return $query
            ->with("categories")
            ->when(
                $search !== "",
                fn($q) => $q->where("name", "like", "%{$search}%"),
            )
            ->when(!empty($itemCategoryIds), function ($q) use (
                $itemCategoryIds,
            ) {
                $q->whereHas(
                    "categories",
                    fn($q2) => $q2->whereIn("categories.id", $itemCategoryIds),
                );
            })
            ->orderBy($sortField, $sortDir);
    }
}
