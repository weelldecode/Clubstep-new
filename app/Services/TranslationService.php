<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    public function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        $value = $this->lookup($key, $locale);

        if ($value === null) {
            $fallback = config("app.fallback_locale", "en");
            if ($fallback && $fallback !== $locale) {
                $value = $this->lookup($key, $fallback);
            }
        }

        if ($value === null) {
            return __($key, $replace, $locale);
        }

        return $this->interpolate($value, $replace);
    }

    private function lookup(string $key, string $locale): ?string
    {
        $cacheKey = "translations:{$locale}:" . md5($key);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($key, $locale) {
            return Translation::query()
                ->where("key", $key)
                ->where("locale", $locale)
                ->where("is_active", true)
                ->value("value");
        });
    }

    private function interpolate(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace(":" . $key, (string) $value, $content);
            $content = str_replace("{{ {$key} }}", (string) $value, $content);
            $content = str_replace("{{{$key}}}", (string) $value, $content);
        }

        return $content;
    }
}
