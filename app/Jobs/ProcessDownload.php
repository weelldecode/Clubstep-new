<?php

namespace App\Jobs;

use App\Models\Item;
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

            $item = $this->download; // relacionamento do download com o item

            $item_collection = Item::where("id", $item->collection_id)->first();

            if (!$item_collection || !$item_collection->file_url) {
                Log::error(
                    "ProcessDownload: Item não encontrado ou sem arquivo para download ID {$item_collection->id}",
                );
                $this->download->update(["status" => "failed"]);
                return;
            }

            // Caminho do zip já pronto
            $zipPath = storage_path("app/public/" . $item_collection->file_url);

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
                "file_path" => $item_collection->file_url,
            ]);

            Log::info(
                "ProcessDownload: Download pronto em {$item_collection->file_url}",
            );
        } catch (\Exception $e) {
            Log::error("ProcessDownload Error: {$e->getMessage()}");
            $this->download->update(["status" => "failed"]);
        }
    }
}
