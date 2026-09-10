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
        abort_if(str_contains($path, '..') || str_contains($path, "\0"), 404);

        $path = ltrim(str_replace('\\', '/', $path), '/');
        abort_unless(str_starts_with($path, 'proofs/'), 404);

        abort_unless(Storage::disk('public')->exists($path), 404);

        $mime = Storage::disk('public')->mimeType($path);

        return Storage::disk('public')->response($path, null, [
            'Content-Type' => $mime,
        ]);
    }
}
