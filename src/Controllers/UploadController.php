<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Models\LteFileStorage;

/**
 * Class HomeController
 *
 * @package Lar\LteAdmin\Controllers
 */
class UploadController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (request()->hasFile('upload') && $file = LteFileStorage::makeFile(request()->file('upload'))) {

            return response()->json(array(
                'url' => asset($file),
                'uploaded' => 1
            ));
        }

        return response()->json(array(
            'uploaded' => 0
        ));
    }
}
