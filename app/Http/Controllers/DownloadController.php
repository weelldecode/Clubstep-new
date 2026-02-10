<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Download;

class DownloadController extends Controller
{
    public function download(Download $download)
    {
        $user = auth()->user();

        if (!method_exists($user, "hasActiveSubscription") || !$user->hasActiveSubscription()) {
            abort(403, "Apenas assinantes podem baixar este arquivo.");
        }

        $filePathRaw = (string) $download->file_path;

        if (str_starts_with($filePathRaw, "http://") || str_starts_with($filePathRaw, "https://")) {
            return redirect()->away($filePathRaw);
        }

        $filePath = storage_path("app/public/" . ltrim($filePathRaw, "/"));

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, basename($filePath));
    }
}
