<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Commentcontroller;
use App\Http\Controllers\API\FollowController;
use App\Http\Controllers\API\Likescontroller;
use App\Http\Controllers\API\LogActivitycontroller;
use App\Http\Controllers\API\Postcontroller;
use App\Http\Controllers\API\ProfileController;
use App\Http\Middleware\LogActivityMiddleware;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Profiler\Profile;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:api', LogActivityMiddleware::class])->group(function () {

    Route::get('/me', function (Request $request) {
        return [
            'user' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'avatar' => $request->user()->avatar,
                'username' => $request->user()->username
            ],
        ];
    });

    // Route::get('/posts/my-posts', [Postcontroller::class, 'my_post']);
    Route::get('/posts/{id}', [Postcontroller::class, 'detail']);
    Route::post('/posts', [Postcontroller::class, 'post'])->name('post');
    Route::get('/posts', [Postcontroller::class, 'postExplore'])->name('post');

    Route::get('/comments/{post_id}', [Commentcontroller::class, 'get_comment_by_post']);
    Route::post('/comments', [Commentcontroller::class, 'post'])->name('comments');
    Route::delete('/comments{id}', [Commentcontroller::class, 'delete'])->name('comments');

    Route::post(('/likes/{post_id}'), [Likescontroller::class, 'post'])->name('likes');
    Route::delete('/unlike/{post_id}', [Likescontroller::class, 'delete'])->name('unlike');

    Route::get('/log-activity', [LogActivitycontroller::class, 'get']);


    Route::get(('/profile/{username}'), [ProfileController::class, 'find_by_username'])->name('profile');
    Route::put(('/profile'), [ProfileController::class, 'update'])->name('profile');
    Route::post(('/profile/avatar'), [ProfileController::class, 'update_avatar'])->name('profile/avatar');

    Route::post('/follow/{following_id}', [FollowController::class, 'follow'])->name('follow');
    Route::delete('/unfollow/{following_id}', [FollowController::class, 'unfollow'])->name('unfollow');

    Route::get('/search/{search}', [ProfileController::class, 'search'])->name('search');

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/test', function () {
    return ['message' => 'API working'];
});
