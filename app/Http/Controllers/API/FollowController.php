<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersFollows;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(Request $request)
    {
    
        $follow = UsersFollows::create([
            'user_id' => $request->user()->id,
            'following_id' => $request->following_id,
        ]);

        // update count follower
        User::where('id', $request->following_id)->update([
            'count_followers' => User::where('id', $request->following_id)->first()->count_follower + 1,
        ]);

        // update count following
        User::where('id', $request->user()->id)->update([
            'count_following' => User::where('id', $request->user()->id)->first()->count_following + 1,
        ]);

        return response()->json([
            'message' => 'Follow created successfully',
            'follow' => $follow
        ]);
    }
}
