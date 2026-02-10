<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Enums\CollectionStatus;
use App\Domain\Collections\Enums\CollectionVisibility;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $locales = array_keys(
            config("laravellocalization.supportedLocales", []),
        );
        if (empty($locales)) {
            $locales = [app()->getLocale()];
        }

        $urls = [];

        $addUrl = function (
            string $loc,
            ?Carbon $lastmod = null,
            ?string $changefreq = null,
            ?string $priority = null,
        ) use (&$urls) {
            $urls[] = [
                "loc" => $loc,
                "lastmod" => $lastmod?->toAtomString(),
                "changefreq" => $changefreq,
                "priority" => $priority,
            ];
        };

        $collections = Collection::query()
            ->select(["slug", "updated_at"])
            ->where("status", CollectionStatus::Published)
            ->where("visibility", CollectionVisibility::Public)
            ->get();

        $tags = Tag::query()
            ->select(["slug", "updated_at"])
            ->whereNotNull("slug")
            ->whereHas("collections", function ($query) {
                $query
                    ->where("status", CollectionStatus::Published)
                    ->where("visibility", CollectionVisibility::Public);
            })
            ->get();

        $categories = Category::query()
            ->select(["slug", "updated_at"])
            ->whereNotNull("slug")
            ->whereHas("collections", function ($query) {
                $query
                    ->where("status", CollectionStatus::Published)
                    ->where("visibility", CollectionVisibility::Public);
            })
            ->get();

        $users = User::query()
            ->select(["slug", "updated_at", "is_private"])
            ->whereNotNull("slug")
            ->where(function ($query) {
                $query->whereNull("is_private")->orWhere("is_private", false);
            })
            ->get();

        foreach ($locales as $locale) {
            $localized = fn(string $path) => LaravelLocalization::getLocalizedURL(
                $locale,
                $path,
                [],
                true,
            );

            $addUrl(
                $localized(route("home", [], false)),
                now(),
                "daily",
                "1.0",
            );
            $addUrl(
                $localized(route("plans", [], false)),
                now(),
                "weekly",
                "0.8",
            );
            $addUrl(
                $localized(route("collection.index", [], false)),
                now(),
                "daily",
                "0.9",
            );

            foreach ($collections as $collection) {
                $addUrl(
                    $localized(
                        route(
                            "collection.show",
                            ["collection" => $collection->slug],
                            false,
                        ),
                    ),
                    $collection->updated_at,
                    "weekly",
                    "0.7",
                );
            }

            foreach ($tags as $tag) {
                $addUrl(
                    $localized(
                        route("collection.tag", ["tag" => $tag->slug], false),
                    ),
                    $tag->updated_at,
                    "weekly",
                    "0.6",
                );
            }

            foreach ($categories as $category) {
                $addUrl(
                    $localized(
                        route(
                            "collection.category",
                            ["category" => $category->slug],
                            false,
                        ),
                    ),
                    $category->updated_at,
                    "weekly",
                    "0.6",
                );
            }

            foreach ($users as $user) {
                $addUrl(
                    $localized(
                        route("profile.user", ["user" => $user->slug], false),
                    ),
                    $user->updated_at,
                    "weekly",
                    "0.5",
                );
            }
        }

        return response()
            ->view("sitemap", ["urls" => $urls])
            ->header("Content-Type", "application/xml; charset=UTF-8");
    }
}
