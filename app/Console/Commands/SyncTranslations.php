<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;

class SyncTranslations extends Command
{
    protected $signature = 'translations:sync {--locale= : Locale to sync (pt_BR or en)}';

    protected $description = 'Sync translation JSON files into database';

    public function handle(): int
    {
        $basePath = base_path('lang');
        $locale = $this->option('locale');

        $locales = [];
        if ($locale) {
            $locales[] = $locale;
        } else {
            $locales = ['pt_BR', 'en'];
        }

        foreach ($locales as $loc) {
            $file = $basePath . DIRECTORY_SEPARATOR . $loc . '.json';
            if (!file_exists($file)) {
                $this->warn("Arquivo não encontrado: {$file}");
                continue;
            }

            $json = json_decode(file_get_contents($file), true);
            if (!is_array($json)) {
                $this->error("JSON inválido: {$file}");
                continue;
            }

            foreach ($json as $key => $value) {
                Translation::updateOrCreate(
                    ["key" => $key, "locale" => $loc],
                    ["value" => $value, "is_active" => true],
                );
            }

            $this->info("Sincronizado: {$loc}");
        }

        return self::SUCCESS;
    }
}
