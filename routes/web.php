<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ChannelPlaylistController;
use App\Http\Controllers\ChannelSearchController;
use App\Http\Controllers\ChannelVideoController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\JobDetailsController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTokensController;
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

Route::resource('videos', VideoController::class)->only(['index', 'show', 'destroy']);
Route::resource('channels', ChannelController::class)->except(['create', 'store']);
Route::resource('channels.videos', ChannelVideoController::class)->only(['index']);
Route::resource('channels.playlists', ChannelPlaylistController::class)->only(['index']);
Route::resource('channels.search', ChannelSearchController::class)->only(['index']);
Route::resource('playlists', PlaylistController::class)->except(['create', 'store']);

Route::get('/images/thumbs/{video}', [ImageController::class, 'showVideoThumb']);
Route::get('/images/posters/{video}', [ImageController::class, 'showVideoPoster']);
Route::get('/images/channels/{channel}', [ImageController::class, 'showChannel']);

Auth::routes();

Route::resource('users', UserController::class)->only(['index', 'show', 'update']);
Route::resource('users.tokens', UserTokensController::class)->only(['store', 'destroy']);

Route::resource('favorites', FavoritesController::class)->only(['index']);
Route::post('/favorites/toggleVideo', [FavoritesController::class, 'toggleVideo']);
Route::post('/favorites/togglePlaylist', [FavoritesController::class, 'togglePlaylist']);
Route::post('/favorites/toggleChannel', [FavoritesController::class, 'toggleChannel']);

Route::resource('job-details', JobDetailsController::class)->only(['index']);

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/playlists', [AdminController::class, 'playlistImport']);
    Route::post('/videos', [AdminController::class, 'videoImport']);
    Route::post('/channels', [AdminController::class, 'channelImport']);
    Route::get('/missing', [AdminController::class, 'missing']);
    Route::get('/queue', [AdminController::class, 'queue']);
});

Route::get('/robots.txt', [MetaController::class, 'robots']);
Route::get('/opensearch.xml', [MetaController::class, 'openSearch']);
