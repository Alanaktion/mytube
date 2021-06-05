<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessChannelImport;
use App\Jobs\ProcessPlaylistImport;
use App\Models\Video;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

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
            'videoCount' => $this->fastCount('videos'),
            'channelCount' => $this->fastCount('channels'),
            'playlistCount' => $this->fastCount('playlists'),
            'missingCount' => Video::whereNull('file_path')->count(),
        ]);
    }

    /**
     * Get an approximate row count for an InnoDB table
     *
     * Due to a limitation in Laravel/PDO, the table name is NOT parameterized,
     * and MUST be a trusted value (not from the user).
     */
    protected function fastCount(string $table): ?int
    {
        $connection = config('database.default');
        if (config("database.connections.{$connection}.driver") == 'mysql') {
            $result = DB::select("show table status like '{$table}'");
            return $result[0]->Rows ?? null;
        }
        return DB::select("select count(*) as count from {$table}")[0]->count ?? null;
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
            $source = 'youtube';
            if (str_contains($id, '/')) {
                if (strpos($id, 'twitch.tv') !== false) {
                    if (preg_match('/^(https:\/\/)?(www\.)?twitch\.tv\/videos\/(v?[0-9]+)/i', $id, $matches)) {
                        $id = Str::start($matches[3], 'v');
                    }
                    $source = 'twitch';
                } elseif (strpos($id, 'twitter.com') !== false) {
                    if (preg_match('/^(https:\/\/)?(www\.)?twitter\.com\/([^\/]+)\/status\/([0-9]+)/i', $id, $matches)) {
                        $id = $matches[4];
                    }
                    $source = 'twitter';
                } elseif (strpos($id, 'floatplane.com') !== false) {
                    if (preg_match('/^(https:\/\/)?(www\.)?floatplane\.com\/post\/([0-9a-z_-]{10})/i', $id, $matches)) {
                        $id = $matches[3];
                    }
                    $source = 'floatplane';
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
                } elseif ($source == 'twitch' || preg_match('/^v?[0-9]{9}$/', $id)) {
                    Video::importTwitch(ltrim($id, 'v'));
                } elseif ($source == 'twitter' || preg_match('/^[0-9]{19}$/', $id)) {
                    Video::importTwitter($id);
                } elseif ($source == 'floatplane' || preg_match('/^[0-9a-z_-]{10}$/i', $id)) {
                    Video::importFloatplane($id);
                } else {
                    continue;
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
            ->latest('id');
        return view('admin.missing', [
            'title' => __('Administration'),
            'videos' => $videos->paginate(),
        ]);
    }

    public function queue()
    {
        $queuedJobs = [];
        if (config('queue.default') == 'database') {
            $rows = DB::table(config('queue.connections.database.table'))
                ->get();
            foreach ($rows as $row) {
                $payload = json_decode($row->payload);
                $queuedJobs[] = (object)[
                    'queue' => $row->queue,
                    'name' => basename(strtr($payload->displayName, '\\', '/')),
                    'payload' => $payload,
                    'attempts' => $row->attempts,
                    'reserved_at' => $row->reserved_at ? Carbon::createFromTimestampUTC($row->reserved_at) : null,
                    'available_at' => Carbon::createFromTimestampUTC($row->available_at),
                ];
            }
        } elseif (config('queue.default') == 'redis') {
            $queues = Redis::keys('queues:*');
            foreach ($queues as $queue) {
                if (substr($queue, -7) == ':notify') {
                    continue;
                }
                $queue = substr($queue, strpos($queue, ':') + 1);
                $jobs = Redis::lrange('queues:' . $queue, 0, -1);
                foreach ($jobs as $job) {
                    $row = json_decode($job);
                    $queuedJobs[] = (object)[
                        'queue' => $queue,
                        'name' => basename(strtr($row->displayName, '\\', '/')),
                        'payload' => $row->data,
                        'attempts' => $row->attempts,
                    ];
                }
            }
        }
        return view('admin.queue', [
            'title' => __('Administration'),
            'queue' => $queuedJobs,
        ]);
    }
}
