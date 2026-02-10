<?php

namespace App\Jobs;

use App\Models\Collection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Download;
use Illuminate\Support\Facades\Log;

class ProcessDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $download;

    public function __construct(Download $download)
    {
        $this->download = $download;
    }

    public function handle()
    {
        try {
            $this->download->update(["status" => "processing"]);

            $collection =
                $this->download->relationLoaded("collection") ||
                $this->download->collection
                    ? $this->download->collection
                    : Collection::find($this->download->collection_id);

            if (!$collection || !$collection->file_url) {
                Log::error(
                    "ProcessDownload: Coleção não encontrada ou sem arquivo para download ID {$this->download->collection_id}",
                );
                $this->download->update(["status" => "failed"]);
                return;
            }

            $fileUrl = (string) $collection->file_url;

            if (str_starts_with($fileUrl, "http://") || str_starts_with($fileUrl, "https://")) {
                $this->download->update([
                    "status" => "ready",
                    "file_path" => $fileUrl,
                ]);
                Log::info("ProcessDownload: Download pronto em {$fileUrl}");
                return;
            }

            // Caminho do zip já pronto
            $zipPath = storage_path("app/public/" . ltrim($fileUrl, "/"));

            if (!file_exists($zipPath)) {
                Log::error(
                    "ProcessDownload: Arquivo zip não encontrado em {$zipPath}",
                );
                $this->download->update(["status" => "failed"]);
                return;
            }

            // Atualiza o download para pronto, apontando para o mesmo zip
            $this->download->update([
                "status" => "ready",
                "file_path" => ltrim($fileUrl, "/"),
            ]);

            Log::info(
                "ProcessDownload: Download pronto em {$collection->file_url}",
            );
        } catch (\Exception $e) {
            Log::error("ProcessDownload Error: {$e->getMessage()}");
            $this->download->update(["status" => "failed"]);
        }
    }
}
