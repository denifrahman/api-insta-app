<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LogActivity;
use Illuminate\Http\Request;

class LogActivitycontroller extends Controller
{
    public function get(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $logActivities = LogActivity::where('user_id', $request->user()->id)->latest()->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json([
            'result' => $logActivities
        ]);
    }
}
