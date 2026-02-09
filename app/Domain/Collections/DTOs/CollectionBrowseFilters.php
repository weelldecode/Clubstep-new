<?php

namespace App\Domain\Collections\DTOs;

final class CollectionBrowseFilters
{
    public function __construct(
        public readonly string $search = "",
        public readonly array $collectionCategoryIds = [],
        public readonly array $collectionTagIds = [],
        public readonly array $itemTagIds = [],
        public readonly string $sortField = "name",
        public readonly string $sortDirection = "asc",
    ) {}

    public static function from(array $data): self
    {
        return new self(
            search: (string) ($data["search"] ?? ""),
            collectionCategoryIds: array_values(
                $data["selectedCollectionCategories"] ?? [],
            ),
            collectionTagIds: array_values(
                $data["selectedCollectionTags"] ?? [],
            ),
            itemTagIds: array_values($data["selectedItemTags"] ?? []),
            sortField: (string) ($data["sortField"] ?? "name"),
            sortDirection: (string) ($data["sortDirection"] ?? "asc"),
        );
    }
}
