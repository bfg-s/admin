<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Models\AdminFileStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * System controller admin panel for downloading and deleting files.
 */
class UploadController extends Controller
{
    /**
     * Endpoint for downloading files.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->hasFile('editormd-image-file')) {
            if ($file = AdminFileStorage::makeFile($request->file('editormd-image-file'))) {
                return response()->json([
                    'success' => 1,
                    'message' => 'Uploaded',
                    'url' => '/'.$file,
                ]);
            }

            return response()->json([
                'success' => 0,
                'message' => 'Error',
            ]);
        }

        if ($request->hasFile('upload')) {
            if ($file = AdminFileStorage::makeFile($request->file('upload'))) {
                return response()->json([
                    'url' => asset($file),
                    'file' => $file,
                    'uploaded' => 1,
                ]);
            }
        }

        return response()->json([
            'uploaded' => 0,
        ]);
    }

    /**
     * Endpoint for deleting files.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function drop(Request $request): JsonResponse
    {
        $file = $request->input('file');

        if ($file) {
            $file = preg_replace('/^\/uploads\//', '', $file);
            $storeFile = AdminFileStorage::where('file_name', $file)->first();

            if ($storeFile) {
                $storeFile->dropFile();
                return response()->json([
                    'drop' => 1,
                ]);
            }

            die;
        }

        return response()->json([
            'drop' => 0,
        ]);
    }
}
