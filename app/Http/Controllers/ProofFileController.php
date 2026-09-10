<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProofFileController extends Controller
{
    /**
     * Stream a file from the "public" storage disk directly,
     * without relying on the public/storage symlink.
     */
    public function show(string $path): StreamedResponse
    {
        // Prevent path traversal (e.g. ../../.env)
        $path = str_replace(['..', "\0"], '', $path);

        abort_unless(Storage::disk('public')->exists($path), 404);

        $mime = Storage::disk('public')->mimeType($path);

        return Storage::disk('public')->response($path, null, [
            'Content-Type' => $mime,
        ]);
    }
}
