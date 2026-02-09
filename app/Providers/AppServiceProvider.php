<?php

namespace App\Providers;

use App\Models\Collection;
use App\Observers\CollectionObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!function_exists('t')) {
            require_once app_path('helpers.php');
        }

        $user = Auth::user();
        if ($user && $user->locale) {
            app()->setLocale($user->locale);
        }

        Collection::observe(CollectionObserver::class);

        View::composer("*", function ($view) {
            $user = Auth::user();
            $showSubscriptionModal = false;

            if (
                $user &&
                $user->hasActiveSubscription() &&
                !$user->subscription_modal_shown
            ) {
                $showSubscriptionModal = true;
            }

            $view->with("showSubscriptionModal", $showSubscriptionModal);
        });
    }
}
