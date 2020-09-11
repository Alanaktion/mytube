<?php

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

Route::get('/', 'HomeController@index');
Route::get('/search', 'HomeController@search');

Route::get('/videos', 'HomeController@videos');
Route::get('/videos/{video:uuid}', 'HomeController@videoShow');

Route::get('/channels', 'HomeController@channels');
Route::get('/channels/{channel:uuid}', 'HomeController@channelShow');

Route::get('/playlists', 'HomeController@playlists');
Route::get('/playlists/{playlist:uuid}', 'HomeController@playlistShow');

Auth::routes();

Route::get('/admin', 'AdminController@index');
