<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ImageReportController;
use App\Http\Controllers\PostViewController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\ReportClientController;
use App\Http\Controllers\EmailController;
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
    Route::get('/user-role/role', [UserController::class, 'userrole']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::get('/{id}/name', [UserController::class, 'getName']);
    Route::post('', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::post('/change-password', [UserController::class, 'changePassWord']);
});

Route::post('/send-email', [EmailController::class, 'sendEmail']);
Route::post('/send-email-password', [EmailController::class, 'sendEmailPassword']);

Route::group(['prefix' => 'report'], function () {
    Route::get('', [ReportClientController::class, 'index']);
    Route::get('/filter', [PostController::class, 'filter']);
    Route::get('/user/{id}', [ReportClientController::class, 'getReportByUser']);
    Route::get('/{id}', [ReportClientController::class, 'show']);
    Route::post('', [ReportClientController::class, 'store']);
    Route::put('/{id}', [ReportClientController::class, 'update']);
    Route::patch('/{id}', [PostController::class, 'updateStatus']);
    Route::delete('/{id}', [ReportClientController::class, 'destroy']);
    Route::get('/user/{id}/count', [PostController::class, 'totalPostByUser']);
});

// Route for posts
Route::group(['prefix' => 'posts'], function () {
    Route::get('', [PostController::class, 'index']);
    Route::get('/filter', [PostController::class, 'filter']);
    Route::get('/filter/user/{id}', [PostController::class, 'filterByUser']);
    Route::get('/filtersold', [PostController::class, 'filtersold']);
    Route::get('/filterSoldUser/user/{id}', [PostController::class, 'filtersoldByUser']);
    Route::get('/postsold/user/{id}', [PostController::class, 'postsoldbyuser']);
    Route::get('/user/{id}', [PostController::class, 'getPostByUser']);
    Route::get('/user-status/{id}', [PostController::class, 'getPostStatusByUser']);
    Route::get('/user-status2/{id}', [PostController::class, 'getPostStatus2ByUser']);
    Route::get('/user-status3/{id}', [PostController::class, 'getPostStatus3ByUser']);
    Route::get('/user-sold/{id}', [PostController::class, 'getPostSoldByUser']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::get('/postbyid/{id}', [PostController::class, 'showpostByid']);
    Route::post('', [PostController::class, 'store']);
    Route::put('/{id}', [PostController::class, 'update']);
    Route::patch('/{id}', [PostController::class, 'updateStatus']);
    Route::patch('/status2/{id}', [PostController::class, 'updateStatus2']);
    Route::delete('/{id}', [PostController::class, 'destroy']);
    Route::get('/user/{id}/count', [PostController::class, 'totalPostByUser']);
    Route::post('/sold_status/{id}', [PostController::class, 'updateSold']);
    Route::post('/avater/update-avatar', [UserController::class, 'updateAvatar']);

});
Route::group(['prefix' => 'client'], function(){
    Route::get('', [ClientController::class, 'index']);
    Route::get('/{id}', [ClientController::class, 'show']);
    Route::post('/{id}', [ClientController::class, 'update']);
    Route::delete('/{id}', [ClientController::class, 'destroy']);
    Route::get('/export/client', [ClientController::class, 'export']);
});

Route::group(['prefix' => 'config'], function(){
    Route::get('', [ConfigController::class, 'detail']);
    Route::post('', [ConfigController::class, 'update']);
});
Route::group(['prefix' => 'client'], function(){
    Route::get('', [ClientController::class, 'index']);
    Route::delete('/{id}', [ClientController::class, 'destroy']);
});

Route::group(['prefix' => 'config'], function(){
    Route::get('', [ConfigController::class, 'detail']);
    Route::post('', [ConfigController::class, 'update']);
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
Route::post('/updateMultiplereport', [ImageReportController::class, 'update']);
Route::post('/uploadMultiplereport', [ImageReportController::class, 'upload']);

Route::post('/updateMultiplecard', [ReportCardController::class, 'update']);
Route::post('/uploadMultiplecard', [ReportCardController::class, 'upload']);


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
