<?php

namespace App\Providers;

use App\Models\Collection;
use App\Policies\CollectionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Collection::class => CollectionPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
