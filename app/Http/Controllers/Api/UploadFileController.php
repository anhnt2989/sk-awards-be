<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUploadedFileRequest;
use App\Services\FileUploadService;
use Throwable;

class UploadFileController extends Controller
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * @throws Throwable
     */
    public function store(StoreUploadedFileRequest $request)
    {
        $data = [];

        foreach ($request->file('files') as $file) {
            $uploadedFile = $this->fileUploadService->upload(
                file: $file,
                userId: $request->user()?->id,
            );

            $data[] = [
                'id' => $uploadedFile->id,
                'url' => $uploadedFile->url,
                'original_name' => $uploadedFile->original_name,
            ];
        }

        return response()->json([
            'message' => 'Upload thành công.',
            'data' => $data,
        ], 201);
    }
}
