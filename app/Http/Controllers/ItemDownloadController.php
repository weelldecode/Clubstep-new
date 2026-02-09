<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemDownloadController extends Controller
{
    public function download(Item $item)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, "Faça login para baixar.");
        }

        if ($item->type !== "sites") {
            abort(403, "Download disponível apenas para itens comprados.");
        }

        $hasPurchase = $user->orders()
            ->where("status", "paid")
            ->whereHas("items", fn($q) => $q->where("item_id", $item->id))
            ->exists();

        if (!$hasPurchase) {
            abort(403, "Você ainda não comprou este item.");
        }

        if (!$item->file_url) {
            abort(404);
        }

        if (str_starts_with($item->file_url, "http://") || str_starts_with($item->file_url, "https://")) {
            return redirect()->away($item->file_url);
        }

        $filePath = storage_path("app/public/" . ltrim((string) $item->file_url, "/"));

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, basename($filePath));
    }
}
