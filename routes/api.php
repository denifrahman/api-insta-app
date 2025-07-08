<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Commentcontroller;
use App\Http\Controllers\API\Likescontroller;
use App\Http\Controllers\API\LogActivitycontroller;
use App\Http\Controllers\API\Postcontroller;
use App\Http\Middleware\LogActivityMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:api', LogActivityMiddleware::class])->group(function () {

    Route::get('/me', function (Request $request) {
        return [
            'user' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ],
        ];
    });

    Route::get('/posts/my-posts', [Postcontroller::class, 'my_post']);
    Route::get('/posts/{id}', [Postcontroller::class, 'detail']);
    Route::post('/posts', [Postcontroller::class, 'post'])->name('post');

    Route::get('/comments/{post_id}', [Commentcontroller::class, 'get_comment_by_post']);
    Route::post('/comments', [Commentcontroller::class, 'post'])->name('comments');
    Route::delete('/comments{id}', [Commentcontroller::class, 'delete'])->name('comments');

    Route::post(('/likes/{post_id}'), [Likescontroller::class, 'post'])->name('likes');
    Route::delete('/unlike/{post_id}', [Likescontroller::class, 'delete'])->name('unlike');

    Route::get('/log-activity', [LogActivitycontroller::class, 'get']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/test', function () {
    return ['message' => 'API working'];
});
