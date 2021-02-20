<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessChannelImport;
use App\Jobs\ProcessPlaylistImport;
use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Video;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            'title' => __('Administration'),
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
            if (str_contains($id, '/')) {
                $query = parse_url($id, PHP_URL_QUERY);
                parse_str($query, $parts);
                if (!empty($parts['list'])) {
                    $id = $parts['list'];
                }
            }
            ProcessPlaylistImport::dispatch($id);
        }

        $message = 'Playlist import started.';
        if (config('queue.default') == 'sync') {
            $message = 'Playlists imported.';
        }
        return redirect('/admin')->with('message', $message);
    }

    public function videoImport(Request $request)
    {
        $request->validate([
            'videoIds' => 'required|string',
        ]);
        $ids = array_map('trim', explode("\n", $request->input('videoIds')));
        $count = count($ids);
        $success = 0;
        foreach ($ids as $id) {
            if (str_contains($id, '/')) {
                if (strpos($id, 'twitch.tv') !== false) {
                    if (preg_match('/^(https:\/\/)?(www\.)?twitch\.tv\/videos\/(v[0-9]+)/i', $id, $matches)) {
                        $id = $matches[3];
                    }
                } elseif (strpos($id, 'floatplane.com') !== false) {
                    if (preg_match('/^(https:\/\/)?(www\.)?floatplane\.com\/post\/([0-9a-z_-]{10})/i', $id, $matches)) {
                        $id = $matches[3];
                    }
                } elseif (strpos($id, 'v=') !== false) {
                    $query = parse_url($id, PHP_URL_QUERY);
                    parse_str($query, $parts);
                    if (!empty($parts['v'])) {
                        $id = $parts['v'];
                    }
                } else {
                    $id = basename($id);
                }
            }
            try {
                if (preg_match('/^[0-9a-z_-]{11}$/i', $id)) {
                    Video::importYouTube($id);
                } elseif (preg_match('/^v?[0-9]{9}$/', $id)) {
                    Video::importTwitch($id);
                } elseif (preg_match('/^[0-9a-z_-]{10}$/i', $id)) {
                    Video::importFloatplane($id);
                }
                $success++;
            } catch (Exception $e) {
                Log::warning($e->getMessage());
            }
        }

        $message = "{$success} of {$count} videos imported.";
        return redirect('/admin')->with('message', $message);
    }

    public function channelImport(Request $request)
    {
        // TODO: handle Twitch and Floatplane URLs
        $request->validate([
            'channelId' => 'required|string',
        ]);
        $id = $request->input('channelId');
        if (str_contains($id, '/')) {
            preg_match('@https?://(www\.)?(youtube\.com|youtu\.be)/channel/([0-9a-z]{8,})@i', $id, $matches);
            if (!empty($matches[3])) {
                $id = $matches[3];
            }
        }
        ProcessChannelImport::dispatch(
            $id,
            $request->boolean('videos'),
            $request->boolean('playlists')
        );

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
            'title' => __('Administration'),
            'videos' => $videos->paginate(),
        ]);
    }
}
