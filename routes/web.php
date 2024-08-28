<?php

use Illuminate\Support\Facades\Route;
use App\Events\UserLoggedOut;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-event', function () {
    event(new UserLoggedOut(3)); // Thay 1 bằng ID người dùng thực tế
    return 'Event has been fired!';
});