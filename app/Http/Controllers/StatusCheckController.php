<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class StatusCheckController extends Controller
{
    public function status()
    {
        return response()->json([
            'status' => 'ok',
        ], 200);
    }
}
