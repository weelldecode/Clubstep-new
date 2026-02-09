<?php

namespace App\Domain\Home\Queries;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Item;
use App\Models\User;
use App\Support\Cache\CacheKeys;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;

final class HomeFeedQuery
{
    /**
     * Retorna os blocos da Home.
     * - guest: retorna datasets globais e dosSeguidos vazio
     * - auth: inclui feed de seguidos
     */
    public function run(?int $userId = null, array $followingIds = []): array
    {
        $recomendadas = Cache::remember(
            CacheKeys::homeRecommended(),
            now()->addMinutes(5),
            fn() => Collection::with("user")->latest()->take(8)->get(),
        );

        $featuredArtists = Cache::remember(
            CacheKeys::homeFeaturedArtists(),
            now()->addMinutes(10),
            fn() => User::withCount("collections")
                ->orderByDesc("collections_count")
                ->take(8)
                ->get(),
        );

        $categories = Cache::remember(
            CacheKeys::homeCategoriesWithCount(),
            now()->addMinutes(30),
            fn() => Category::query()
                ->where(function ($q) {
                    $q->where("type", "collection")->orWhereNull("type");
                })
                ->withCount(["collections", "legacyCollections"])
                ->with([
                    "collections" => fn($q) => $q
                        ->with([
                            "items" => fn($iq) => $iq->latest()->limit(1),
                        ])
                        ->latest()
                        ->limit(1),
                    "legacyCollections" => fn($q) => $q
                        ->with([
                            "items" => fn($iq) => $iq->latest()->limit(1),
                        ])
                        ->latest()
                        ->limit(1),
                ])
                ->get()
                ->map(function ($cat) {
                    $cat->items_count = (int) ($cat->collections_count ?? 0) + (int) ($cat->legacy_collections_count ?? 0);

                    // Mantem compatibilidade com a view atual que usa $cat->collections
                    if (($cat->collections->count() ?? 0) === 0 && ($cat->legacyCollections->count() ?? 0) > 0) {
                        $cat->setRelation("collections", $cat->legacyCollections);
                    }

                    return $cat;
                }),
        );

        $categorias = Cache::remember(
            CacheKeys::homeCategorySections(),
            now()->addMinutes(10),
            fn() => Category::query()
                ->where(function ($q) {
                    $q->where("type", "collection")->orWhereNull("type");
                })
                ->with([
                    "collections" => fn($q) => $q->with("user")->latest()->take(6),
                    "legacyCollections" => fn($q) => $q->with("user")->latest()->take(6),
                ])
                ->take(5)
                ->get()
                ->map(function ($cat) {
                    if (($cat->collections->count() ?? 0) === 0 && ($cat->legacyCollections->count() ?? 0) > 0) {
                        $cat->setRelation("collections", $cat->legacyCollections);
                    }

                    return $cat;
                }),
        );

        $bestSellers = Cache::remember(
            CacheKeys::homeBestSellers(),
            now()->addMinutes(5),
            fn() => Item::query()
                ->select([
                    "items.id",
                    "items.name",
                    "items.price",
                    "items.image_path",
                    "items.images",
                    "items.type",
                    "items.collection_id",
                ])
                ->selectRaw("SUM(order_items.quantity) as sold_qty")
                ->join("order_items", "order_items.item_id", "=", "items.id")
                ->join("orders", "orders.id", "=", "order_items.order_id")
                ->where("orders.status", "paid")
                ->where("items.type", "sites")
                ->groupBy([
                    "items.id",
                    "items.name",
                    "items.price",
                    "items.image_path",
                    "items.images",
                    "items.type",
                    "items.collection_id",
                ])
                ->orderByDesc("sold_qty")
                ->with("collection:id,slug")
                ->take(6)
                ->get(),
        );

        $dosSeguidos = collect();

        if ($userId && !empty($followingIds)) {
            $followHash = substr(
                sha1(json_encode(array_values($followingIds))),
                0,
                12,
            );

            $dosSeguidos = Cache::remember(
                CacheKeys::homeFollowingFeed($userId, $followHash),
                now()->addMinutes(2),
                fn() => Collection::with("user")
                    ->whereIn("user_id", $followingIds)
                    ->latest()
                    ->take(8)
                    ->get(),
            );
        }

        return [
            "recomendadas" => $recomendadas,
            "featuredArtists" => $featuredArtists,
            "categories" => $categories,
            "categorias" => $categorias,
            "bestSellers" => $bestSellers,
            "dosSeguidos" => $dosSeguidos,
        ];
    }
}
