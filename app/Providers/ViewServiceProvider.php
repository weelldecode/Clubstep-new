<?php

namespace App\Providers;

use App\Domain\Collections\Enums\CollectionStatus;
use App\Domain\Collections\Enums\CollectionVisibility;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer("*", function ($view) {
            $categories = Category::query()
                ->whereHas("collections", function ($query) {
                    $query
                        ->where("status", CollectionStatus::Published)
                        ->where("visibility", CollectionVisibility::Public);
                })
                ->with([
                    "collections" => function ($query) {
                        $query
                            ->where("status", CollectionStatus::Published)
                            ->where("visibility", CollectionVisibility::Public);
                    },
                ])
                ->orderBy("name")
                ->get();

            $view->with("globalCategories", $categories);
        });
    }
}
