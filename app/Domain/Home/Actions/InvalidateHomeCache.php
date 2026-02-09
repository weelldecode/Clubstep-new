<?php

namespace App\Domain\Home\Actions;

use App\Support\Cache\CacheKeys;
use Illuminate\Support\Facades\Cache;

final class InvalidateHomeCache
{
    public function run(): void
    {
        Cache::forget(CacheKeys::homeRecommended());
        Cache::forget(CacheKeys::homeFeaturedArtists());
        Cache::forget(CacheKeys::homeCategoriesWithCount());
        Cache::forget(CacheKeys::homeCategorySections());
        // dosSeguidos é por-user e TTL curto -> pode ignorar
    }
}
