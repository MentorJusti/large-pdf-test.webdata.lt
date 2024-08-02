<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class TimeController extends Controller
{
    public function getTime()
    {
        return response()->json([
            'time' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
