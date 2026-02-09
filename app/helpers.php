<?php

use App\Services\TranslationService;

if (!function_exists('t')) {
    function t(string $key, array $replace = [], ?string $locale = null): string
    {
        return app(TranslationService::class)->get($key, $replace, $locale);
    }
}
