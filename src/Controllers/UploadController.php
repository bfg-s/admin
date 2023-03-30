<?php

namespace Admin\Controllers;

use Illuminate\Http\JsonResponse;
use Admin\Models\AdminFileStorage;

class UploadController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        if (request()->hasFile('editormd-image-file')) {
            if ($file = AdminFileStorage::makeFile(request()->file('editormd-image-file'))) {
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

        if (request()->hasFile('upload') && $file = AdminFileStorage::makeFile(request()->file('upload'))) {
            return response()->json([
                'url' => asset($file),
                'uploaded' => 1,
            ]);
        }

        return response()->json([
            'uploaded' => 0,
        ]);
    }
}
