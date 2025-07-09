<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;

class Likescontroller extends Controller
{
    function post(Request $request)
    {

        $exists = Like::where('user_id', $request->user()->id)
            ->where('post_id', $request->post_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'Likes already exists'
            ], 400);
        }
        Like::create([
            'user_id' => $request->user()->id,
            'post_id' => $request->post_id
        ]);

        return response()->json([
            'message' => 'Like created successfully'
        ]);
    }

    function delete(Request $request)
    {
        Like::where('user_id', $request->user()->id)
            ->where('post_id', $request->post_id)
            ->delete();

        return response()->json([
            'message' => 'Like deleted successfully'
        ]);
    }
}
