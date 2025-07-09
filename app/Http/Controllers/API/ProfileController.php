<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        User::where('id', $request->user()->id)->update([
            'name' => $request->name,
            'bio' => $request->bio
        ]);
        return response()->json([
            'result' => $request->user()
        ]);
    }

    public function update_avatar(Request $request)
    {

        if (!$request->hasFile('image')) {
            throw new \Exception('No image uploaded');
        }
        $file = $request->file('image');
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = str_replace(' ', '-', $filename);
        $extension = $file->getClientOriginalExtension();
        $path = 'avatar/' . $filename . '.' . $extension;
        $uploaded = Storage::disk('s3')->put($path, file_get_contents($file), 'public');

        if (!$uploaded) {
            throw new \Exception('Upload returned false, check S3 credentials and config.');
        }

        $url = config('filesystems.disks.s3.url') . '/' . $path;
        User::where('id', $request->user()->id)->update([
            'avatar' => $url
        ]);
        $request->user()->avatar = $url;
        return response()->json([
            'result' => $request->user()
        ]);
    }

    public function find_by_username(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $user = User::where('username', $request->username)->first();
        if ($user == null) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }
        $posts = Post::where('user_id', $user->id)
            ->latest()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // check is follow
        $is_follow = $request->user()->following()->where('following_id', $user->id)->exists();
        $user->is_follow = $is_follow;

        return response()->json([
            'result' => [
                'posts' => $posts,
                'user' => $user
            ]
        ]);
    }

    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $users = User::where('username', 'like', '%' . $request->search . '%')
            ->orWhere('name', 'like', '%' . $request->search . '%')
            ->latest()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        return response()->json([
            'result' => $users
        ]);
    }
}
