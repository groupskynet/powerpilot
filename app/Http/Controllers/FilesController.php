<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function getFile(Request $request)
    {
        try {
            if (!Storage::disk('s3')->exists($request->path))
                return "";
            $file = Storage::disk('s3')->get($request->path);
            return "data:;base64," . base64_encode($file);
        } catch (Exception $e) {
            return response()->json(['error ->' => $e->getMessage()]);
        }
    }
}
