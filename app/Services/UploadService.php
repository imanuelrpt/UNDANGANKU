<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UploadService
{
    /**
     * Simpan gambar, hasilkan versi full dan thumbnail dengan nama file ter-hash.
     *
     * @return array{path: string, thumbnail_path: string}
     */
    public function storeImage(UploadedFile $file, string $directory, int $maxWidth = 1600): array
    {
        $hash = Str::random(32);
        $fullPath = "{$directory}/{$hash}.jpg";
        $thumbPath = "{$directory}/{$hash}-thumb.jpg";

        $image = Image::make($file);
        $image->resize($maxWidth, $maxWidth, fn ($constraint): bool => $constraint->aspectRatio()->upsize());

        Storage::disk('public')->put($fullPath, (string) $image->encode('jpg', 85));

        $thumbnail = Image::make($file)->fit(400, 400);
        Storage::disk('public')->put($thumbPath, (string) $thumbnail->encode('jpg', 80));

        return [
            'path' => $fullPath,
            'thumbnail_path' => $thumbPath,
        ];
    }

    public function storeAudio(UploadedFile $file, string $directory = 'music'): string
    {
        $path = "{$directory}/".Str::random(32).'.mp3';
        Storage::disk('public')->putFileAs($directory, $file, basename($path));

        return $path;
    }

    public function delete(?string $path): void
    {
        if (filled($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function deleteImage(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        $this->delete($path);
        $this->delete(str_replace('.jpg', '-thumb.jpg', $path));
    }
}
