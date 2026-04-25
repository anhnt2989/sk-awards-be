<?php

namespace App\Services;

use App\Exceptions\FileUploadFailedException;
use App\Models\UploadedFile as UploadedFileModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class FileUploadService
{
    const int MAX_TRY = 3;
    const string DEFAULT_DISK = 'public';
    const string DEFAULT_DIRECTORY = 'uploads';

    /**
     * @throws Throwable
     */
    public function upload(UploadedFile $file, ?string $userId = null, string $disk = self::DEFAULT_DISK, string $baseDirectory = self::DEFAULT_DIRECTORY): UploadedFileModel
    {
        $directory = trim($baseDirectory, '/') . '/' . now()->format('Y/m/d');

        $extension = strtolower($file->getClientOriginalExtension());

        $path = $this->generateUniquePath(
            disk: $disk,
            directory: $directory,
            extension: $extension,
        );

        try {
            return DB::transaction(function () use ($file, $userId, $disk, $path, $extension) {
                Storage::disk($disk)->putFileAs(
                    dirname($path),
                    $file,
                    basename($path)
                );

                return UploadedFileModel::create([
                    'user_id' => $userId,
                    'disk' => $disk,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'extension' => $extension,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            });
        } catch (Throwable $e) {
            Log::error($e);
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }

            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function delete(UploadedFileModel $uploadedFile, bool $deleteRecord = true): bool
    {
        $disk = $uploadedFile->disk;
        $path = $uploadedFile->path;

        try {
            if ($deleteRecord) {
                $uploadedFile->delete();
            }

            if ($path) {
                Storage::disk($disk)->delete($path);
            }

            return true;
        } catch (Throwable $e) {
            Log::error($e);
            return false;
        }
    }

    /**
     * @throws FileUploadFailedException
     */
    private function generateUniquePath(string $disk, string $directory, string $extension): string
    {
        for ($attempt = 1; $attempt <= self::MAX_TRY; $attempt++) {
            $filename = Str::uuid()->toString();

            if ($extension !== '') {
                $filename .= '.' . $extension;
            }

            $path = trim($directory, '/') . '/' . $filename;

            if (!Storage::disk($disk)->exists($path)) {
                return $path;
            }
        }

        throw new FileUploadFailedException();
    }
}
