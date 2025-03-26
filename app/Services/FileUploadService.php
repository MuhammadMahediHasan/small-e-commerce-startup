<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Upload an image and return its storage path.
     */
    public function upload(UploadedFile $image, $directory = 'files'): string
    {
        $name = time() . '.' . $image->getClientOriginalExtension();


        return Storage::disk('public')->putFile("$directory/$name", $image);
    }

    /**
     * Delete an image from storage.
     */
    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
