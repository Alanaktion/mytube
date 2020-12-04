<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessChannelImport;
use App\Jobs\ProcessPlaylistImport;
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
        return view('admin.index', [
            'videoCount' => Video::count(),
            'channelCount' => Channel::count(),
            'playlistCount' => Playlist::count(),
            'missingCount' => Video::whereNull('file_path')->count(),
        ]);
    }

    public function playlistImport(Request $request)
    {
        $request->validate([
            'playlistIds' => 'required|string',
        ]);
        $ids = array_map('trim', explode("\n", $request->input('playlistIds')));
        foreach ($ids as $id) {
            ProcessPlaylistImport::dispatch($id)->onQueue('import');
        }

        $message = 'Playlist import started.';
        if (config('queue.default') == 'sync') {
            $message = 'Playlists imported.';
        }
        return redirect('/admin')->with('message', $message);
    }

    public function channelImport(Request $request)
    {
        $request->validate([
            'channelId' => 'required|string',
        ]);
        ProcessChannelImport::dispatch(
            $request->input('channel_id'),
            $request->boolean('videos'),
            $request->boolean('playlists')
        )->onQueue('import');

        $message = 'Channel import started.';
        if (config('queue.default') == 'sync') {
            $message = 'Channel imported.';
        }
        return redirect('/admin')->with('message', $message);
    }

    public function missing()
    {
        $videos = Video::with(['channel', 'playlists'])
            ->whereNull('file_path')
            ->orderBy('created_at', 'desc');
        return view('admin.missing', [
            'videos' => $videos->paginate(),
        ]);
    }
}
