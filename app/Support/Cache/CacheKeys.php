<?php

namespace App\Support\Cache;

final class CacheKeys
{
    // Bump quando quiser “invalidar tudo” sem caçar chaves
    public const V = "v3";

    public static function homeRecommended(): string
    {
        return "home:" . self::V . ":recomendadas";
    }

    public static function homeFeaturedArtists(): string
    {
        return "home:" . self::V . ":featuredArtists";
    }

    public static function homeCategoriesWithCount(): string
    {
        return "home:" . self::V . ":categories_with_count";
    }

    public static function homeCategorySections(): string
    {
        return "home:" . self::V . ":categorias_sections";
    }

    public static function homeBestSellers(): string
    {
        return "home:" . self::V . ":best_sellers";
    }

    public static function homeFollowingFeed(
        int $userId,
        string $followHash,
    ): string {
        return "home:" . self::V . ":dosSeguidos:user:$userId:$followHash";
    }
}
