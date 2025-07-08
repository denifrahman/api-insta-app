<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;

class LogActivityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $user = Auth::user();
        if ($user) {
            $routeName = $request->route()->getName();
            $method = $request->method();

            $logType = null;
            $description = null;
            
            if (str_contains($routeName, 'comments') && $method === 'POST') {
                $logType = 'comments';
                $description = 'User commented on a post.';
            } elseif (str_contains($routeName, 'comments') && $method === 'DELETE') {
                $logType = 'delete_comment';
                $description = 'User deleted a comment.';
            } elseif (str_contains($routeName, 'likes') && $method === 'POST') {
                $logType = 'like';
                $description = 'User liked a post.';
            } elseif (str_contains($routeName, 'unlike') && $method === 'DELETE') {
                $logType = 'unlike';
                $description = 'User unliked a post.';
            } elseif (str_contains($routeName, 'post') && $method === 'POST') {
                $logType = 'post';
                $description = 'User created a post.';
            }

            if ($logType && $description) {
                LogActivity::create([
                    'user_id' => $user->id,
                    'description' => $description,
                    'log_type' => $logType,
                ]);
            }
        }

        return $response;
    }
}
