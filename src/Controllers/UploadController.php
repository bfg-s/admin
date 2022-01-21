<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Models\LteFileStorage;

class UploadController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (request()->hasFile('editormd-image-file')) {
            if ($file = LteFileStorage::makeFile(request()->file('editormd-image-file'))) {
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

        if (request()->hasFile('upload') && $file = LteFileStorage::makeFile(request()->file('upload'))) {
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
