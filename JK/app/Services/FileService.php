<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileService
{
    /**
     * Upload an uploaded file to the public directory.
     */
    public function upload(UploadedFile $file, string $directory = 'uploads'): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $filename);
        return $directory . '/' . $filename;
    }

    /**
     * Delete a file from the public directory.
     */
    public function delete(?string $path): bool
    {
        if ($path && file_exists(public_path($path))) {
            return unlink(public_path($path));
        }
        return false;
    }
}
