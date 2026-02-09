<?php

namespace App\Providers;

use App\Models\Tag;
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
            $tags = Tag::whereHas("collections")
                ->with("collections") // evita N+1
                ->orderBy("name")
                ->get();

            $view->with("globalCategories", $tags);
        });
    }
}
