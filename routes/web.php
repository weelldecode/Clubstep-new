<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ItemDownloadController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Livewire\Settings\Visibility;
use Livewire\Livewire;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\OrderPaymentController;

Route::group(
    [
        "prefix" => LaravelLocalization::setLocale(),
        "middleware" => [
            "localeSessionRedirect",
            "localizationRedirect",
            "localeViewPath",
        ],
    ],
    function () {
        Route::get("/", App\Livewire\App\Home::class)->name("home");
        Route::get("/billing", App\Livewire\App\Billing::class)->name(
            "billing",
        );
        Route::get("/plans", App\Livewire\App\Plans::class)->name("plans");

        Route::prefix("profile")
            ->name("profile.")
            ->group(function () {
                Route::get(
                    "/{user:slug}",
                    App\Livewire\App\Profile\Profile::class,
                )->name("user");
            });
        Route::get(
            "/downloads",
            App\Livewire\App\Downloads\Manager::class,
        )->name("download");
        Route::get(
            "/item-download/{item}",
            [ItemDownloadController::class, "download"],
        )->middleware(["auth", "verified"])->name("items.download");
        Route::get(
            "/wishlist",
            App\Livewire\App\Wishlist\Index::class,
        )->middleware(["auth", "verified"])->name("wishlist.index");
        Route::get(
            "/cart",
            App\Livewire\App\Cart\Index::class,
        )->middleware(["auth", "verified"])->name("cart.index");
        Route::prefix("collection")
            ->name("collection.")
            ->group(function () {
                // Index
                Route::get(
                    "/",
                    \App\Livewire\App\Collection\Index::class,
                )->name("index");

                // Index filtrado
                Route::get(
                    "/tag/{tag:slug}",
                    \App\Livewire\App\Collection\Index::class,
                )->name("tag");
                Route::get(
                    "/category/{category:slug}",
                    \App\Livewire\App\Collection\Index::class,
                )->name("category");

                // View da collection (model binding)
                Route::get(
                    "/v/{collection:slug}",
                    \App\Livewire\App\Collection\ViewCollection::class,
                )->name("show");
            });

        Route::prefix("checkout")
            ->middleware(["auth", "verified", "subscription"])
            ->name("checkout.")
            ->group(function () {
                Route::get(
                    "/pay/renew/{id}/{sub_id}",
                    App\Livewire\App\Checkout\Renew::class,
                )->name("renew");
                Route::get(
                    "/pay/{id}",
                    App\Livewire\App\Checkout\Pay::class,
                )->name("index");

                // Rota POST para processar o pagamento
                Route::post("/process", [
                    PaymentController::class,
                    "index",
                ])->name("process");
                Route::post("/subscription", [
                    SubscriptionController::class,
                    "renew",
                ])->name("subscription");
            });

        Route::prefix("checkout")
            ->middleware(["auth", "verified"])
            ->name("checkout.")
            ->group(function () {
                Route::get(
                    "/order/{order}",
                    App\Livewire\App\Checkout\OrderPay::class,
                )->name("order");

                Route::post("/order/process", [
                    OrderPaymentController::class,
                    "index",
                ])->name("order.process");
            });

        Route::redirect("settings", "settings/profile");
        Route::prefix("settings")
            ->middleware(["auth", "verified"])
            ->name("settings.")
            ->group(function () {
                Route::get("/profile", Profile::class)->name("profile");
                Route::get("/visibility", Visibility::class)->name(
                    "visibility",
                );
                Route::get("/password", Password::class)->name("password");
                Route::get("/appearance", Appearance::class)->name(
                    "appearance",
                );
            });
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post("/livewire/update", $handle);
        });

        Route::prefix("studio")
            ->name("studio.")
            ->middleware(["auth", "verified", "admin"])
            ->group(function () {
                Route::get("/", App\Livewire\App\Studio\Dashboard::class)->name(
                    "dashboard",
                );
            });
    },
);

Route::prefix("admin")
    ->name("admin.")
    ->middleware(["auth", "role:admin", "permission:admin.access"])
    ->group(function () {
        Route::get("/", \App\Livewire\Admin\Dashboard\Index::class)->name(
            "dashboard",
        );
        Route::prefix("collections")
            ->name("collections.")
            ->group(function () {
                Route::get(
                    "/",
                    \App\Livewire\Admin\Collections\Index::class,
                )->name("index");
                Route::get(
                    "/{collection}/items",
                    \App\Livewire\Admin\Collections\Items::class,
                )->name("items");
            });
        Route::prefix("categories")
            ->name("categories.")
            ->group(function () {
                Route::get(
                    "/",
                    \App\Livewire\Admin\Categories\Index::class,
                )->name("index");
            });
        Route::prefix("tags")
            ->name("tags.")
            ->group(function () {
                Route::get(
                    "/",
                    \App\Livewire\Admin\Tags\Index::class,
                )->name("index");
            });
        Route::prefix("plans")
            ->name("plans.")
            ->group(function () {
                Route::get(
                    "/",
                    \App\Livewire\Admin\Plans\Index::class,
                )->name("index");
            });
        Route::prefix("subscriptions")
            ->name("subscriptions.")
            ->group(function () {
                Route::get(
                    "/",
                    \App\Livewire\Admin\Subscriptions\Index::class,
                )->name("index");
            });
        Route::prefix("payments")
            ->name("payments.")
            ->group(function () {
                Route::get(
                    "/",
                    \App\Livewire\Admin\Payments\Index::class,
                )->name("index");
            });
        Route::prefix("reports")
            ->name("reports.")
            ->group(function () {
                Route::get(
                    "/",
                    \App\Livewire\Admin\Reports\Index::class,
                )->name("index");
            });
        Route::prefix("email-templates")
            ->name("email-templates.")
            ->group(function () {
                Route::get(
                    "/",
                    \App\Livewire\Admin\EmailTemplates\Index::class,
                )->name("index");
            });
        Route::prefix("translations")
            ->name("translations.")
            ->group(function () {
                Route::get(
                    "/",
                    \App\Livewire\Admin\Translations\Index::class,
                )->name("index");
            });
        Route::prefix("users")
            ->name("users.")
            ->group(function () {
                Route::get("/", \App\Livewire\Admin\Users\Index::class)->name(
                    "index",
                );
            });
        Route::prefix("orders")
            ->name("orders.")
            ->group(function () {
                Route::get(
                    "/",
                    \App\Livewire\Admin\Orders\Index::class,
                )->name("index");
            });
    });
require __DIR__ . "/auth.php";
