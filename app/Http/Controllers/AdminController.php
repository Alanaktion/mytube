<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin', [
            'videoCount' => Video::count(),
            'channelCount' => Channel::count(),
            'playlistCount' => Playlist::count(),
        ]);
    }
}
