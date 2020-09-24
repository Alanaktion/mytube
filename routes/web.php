<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [HomeController::class, 'index']);
Route::get('/search', [HomeController::class, 'search']);

Route::get('/videos', [HomeController::class, 'videos']);
Route::get('/videos/{video:uuid}', [HomeController::class, 'videoShow']);

Route::get('/channels', [HomeController::class, 'channels']);
Route::get('/channels/{channel:uuid}', [HomeController::class, 'channelShow']);

Route::get('/playlists', [HomeController::class, 'playlists']);
Route::get('/playlists/{playlist:uuid}', [HomeController::class, 'playlistShow']);

Route::get('/image/thumb/{video:uuid}', [ImageController::class, 'showVideoThumb']);

Auth::routes();

Route::get('/admin', [AdminController::class, 'index']);
