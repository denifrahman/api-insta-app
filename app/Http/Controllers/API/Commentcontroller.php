<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function post(Request $request)
    {

        Comment::create([
            'user_id' => $request->user()->id,
            'post_id' =>  $request->post_id,
            'body' => $request->body
        ]);

        return response()->json([
            'message' => 'Comment created successfully'
        ]);
    }

    public function get_comment_by_post(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $comment = Comment::where('post_id', $request->post_id)
            ->orderBy('created_at', 'desc')
            ->latest()
            ->paginate($perPage);

        if ($comment == null) {
            return response()->json([
                'error' => 'Comment not found'
            ], 404);
        }
        return response()->json([
            'result' => $comment
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
