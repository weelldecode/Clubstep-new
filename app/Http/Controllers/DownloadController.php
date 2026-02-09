<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Download;

class DownloadController extends Controller
{
    public function download(Download $download)
    {
        $user = auth()->user();

        if (!$user->activeSubscription()) {
            abort(403, "Apenas assinantes podem baixar este arquivo.");
        }

        $filePath = storage_path("app/" . $download->file_path);

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, basename($filePath));
    }
}
