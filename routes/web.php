<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\VideoController;
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

Route::get('/videos', [VideoController::class, 'index']);
Route::get('/videos/{video:uuid}', [VideoController::class, 'show']);

Route::get('/channels', [ChannelController::class, 'index']);
Route::get('/channels/{channel:uuid}', [ChannelController::class, 'videos']);
Route::get('/channels/{channel:uuid}/playlists', [ChannelController::class, 'playlists']);
Route::get('/channels/{channel:uuid}/search', [ChannelController::class, 'search']);
Route::get('/channels/{channel:uuid}/about', [ChannelController::class, 'about']);

Route::get('/playlists', [PlaylistController::class, 'index']);
Route::get('/playlists/{playlist:uuid}', [PlaylistController::class, 'show']);

Route::get('/images/thumbs/{video:uuid}', [ImageController::class, 'showVideoThumb']);
Route::get('/images/posters/{video:uuid}', [ImageController::class, 'showVideoPoster']);
Route::get('/images/channels/{channel:uuid}', [ImageController::class, 'showChannel']);

Auth::routes();

Route::get('/admin', [AdminController::class, 'index']);
Route::post('/admin/playlists', [AdminController::class, 'playlistImport']);
Route::post('/admin/channels', [AdminController::class, 'channelImport']);
Route::get('/admin/missing', [AdminController::class, 'missing']);

Route::get('/robots.txt', [MetaController::class, 'robots']);
Route::get('/opensearch.xml', [MetaController::class, 'openSearch']);
