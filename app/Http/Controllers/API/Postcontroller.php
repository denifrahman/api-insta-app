<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class Postcontroller extends Controller
{
    public function post(Request $request)
    {
        try {
            if (!$request->hasFile('image')) {
                throw new \Exception('No image uploaded');
            }
            $file = $request->file('image');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = str_replace(' ', '-', $filename);
            $extension = $file->getClientOriginalExtension();
            $path = 'posts/' . $filename . '.' . $extension;
            $uploaded = Storage::disk('s3')->put($path, file_get_contents($file), 'public');

            if (!$uploaded) {
                throw new \Exception('Upload returned false, check S3 credentials and config.');
            }

            $url = config('filesystems.disks.s3.url') . '/' . $path;
            Post::create([
                'user_id' => $request->user()->id,
                'caption' =>  $request->caption,
                'image_path' => $url
            ]);

            return response()->json([
                'message' => 'Post created successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('S3 Upload Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Upload failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function my_post(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $posts = Post::where('user_id', $request->user()->id)
            ->latest()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        $post_ids = Arr::pluck($posts->items(), 'id');

        $likes = Post::whereIn('id', $post_ids)
            ->withCount('likes')
            ->get()
            ->keyBy('id');

        foreach ($posts as $post) {
            $post->likes_count = $likes[$post->id]->likes_count;
        }
        return response()->json([
            'result' => $posts
        ]);
    }

    public function detail(Request $request)
    {
        $post = Post::find($request->id);
        $post->likes()->where('user_id', $request->user()->id)->first();
        if ($post == null) {
            return response()->json([
                'error' => 'Post not found'
            ], 404);
        }
        return response()->json([
            'result' => [
                'post' => $post,
                'likes' => $post->likes()->count(),
            ]
        ]);
    }
}
