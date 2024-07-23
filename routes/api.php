<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostViewController;

use Illuminate\Support\Facades\Redis;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = Redis::get('user:' . $request->user()->id);
    if (!$user) {
        $user = $request->user();
        Redis::set('user:' . $request->user()->id, json_encode($user));
        Redis::expire('user:' . $request->user()->id, 3600 * 24);
    } else {
        $user = json_decode($user);
    }

    return $request->user();
});

// Route for authentication
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me']);
});

// Route for users
Route::group(['prefix' => 'users'], function () {
    Route::get('', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::get('/{id}/name', [UserController::class, 'getName']);
    Route::post('', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

// Route for posts
Route::group(['prefix' => 'posts'], function () {
    Route::get('', [PostController::class, 'index']);
    Route::get('/filter', [PostController::class, 'filter']);
    Route::get('/user/{id}', [PostController::class, 'getPostByUser']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::post('', [PostController::class, 'store']);
    Route::put('/{id}', [PostController::class, 'update']);
    Route::patch('/{id}', [PostController::class, 'updateStatus']);
    Route::delete('/{id}', [PostController::class, 'destroy']);
    Route::get('/user/{id}/count', [PostController::class, 'totalPostByUser']);
});

// Route for posts type
Route::get('/pending', [PostController::class, 'pending']);
Route::get('/notPending', [PostController::class, 'notPending']);

// Route for upload image
Route::get('/images', [ImageController::class, 'index']);
Route::get('/images/{id}', [ImageController::class, 'show']);
Route::post('/uploadMultiple', [ImageController::class, 'upload']);
Route::post('/updateMultiple', [ImageController::class, 'update']);
Route::post('/uploadMultipleCommentImg', [ImageController::class, 'uploadCommentImg']);


// Route for comments
Route::group(['prefix' => 'comments'], function () {
    Route::get('', [CommentController::class, 'index']);
    Route::get('/{id}', [CommentController::class, 'show']);
    Route::post('', [CommentController::class, 'store']);
    Route::put('/{id}', [CommentController::class, 'update']);
    Route::delete('/{id}', [CommentController::class, 'destroy']);
});

Route::post('/post-views', [PostViewController::class, 'store']);
Route::get('/posts/{id}/views', [PostViewController::class, 'getViews']);
