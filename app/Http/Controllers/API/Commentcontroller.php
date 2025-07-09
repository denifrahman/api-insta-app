<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CommentController extends Controller
{
    public function post(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'body' => 'required|string',
        ]);

        Comment::create([
            'user_id' => $request->user()->id,
            'post_id' =>  $request->post_id,
            'body' => $request->body
        ]);
        $comments =  $this->get_comment_by_post($request);
        return response()->json([
            'message' => 'Comment created successfully',
            'result' => $comments->original['result']
        ]);
    }

    public function get_comment_by_post(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $comments = Comment::where('post_id', $request->post_id)
            ->orderBy('created_at', 'desc')
            ->latest()
            ->paginate($perPage);

        $userIds = Arr::pluck($comments->items(), 'user_id');

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        foreach ($comments as $comment) {
            $comment->user = $users[$comment->user_id];
        }
        if ($comments == null) {
            return response()->json([
                'error' => 'Comment not found'
            ], 404);
        }
        return response()->json([
            'result' => $comments
        ]);
    }

    function delete(Request $request)
    {
        Comment::where('id', $request->id)->delete();
        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
