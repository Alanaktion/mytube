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
            'missingCount' => Video::doesntHave('files')->count(),
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
        $data = $request->validate([
            'playlistIds' => 'required|string',
        ]);
        $ids = array_map('trim', explode("\n", $data['playlistIds']));
        $sources = app()->tagged('sources');
        foreach ($ids as $id) {
            foreach ($sources as $source) {
                /** @var \App\Sources\Source $source */
                if ($source->playlist() === null) {
                    continue;
                }
                $matched = $source->playlist()->matchUrl($id);
                if ($matched || $source->playlist()->matchId($id)) {
                    ProcessPlaylistImport::dispatch($source->getSourceType(), $matched ?? $id);
                    continue 2;
                }
            }
        }

        $message = 'Playlist import started.';
        if (config('queue.default') == 'sync') {
            $message = 'Playlists imported.';
        }
        return redirect('/admin')->with('message', $message);
    }

    public function videoImport(Request $request)
    {
        $data = $request->validate([
            'videoIds' => 'required|string',
        ]);
        $ids = array_map('trim', explode("\n", $data['videoIds']));
        $count = count($ids);
        $success = 0;
        $sources = app()->tagged('sources');
        foreach ($ids as $id) {
            try {
                foreach ($sources as $source) {
                    /** @var \App\Sources\Source $source */
                    $matched = $source->video()->matchUrl($id);
                    if ($matched || $source->video()->matchId($id)) {
                        Video::import($source->getSourceType(), $matched ?? $id);
                        $success++;
                        continue 2;
                    }
                }
            } catch (Exception $e) {
                Log::warning($e->getMessage());
            }
        }

        $message = "{$success} of {$count} videos imported.";
        return redirect('/admin')->with('message', $message);
    }

    public function channelImport(Request $request)
    {
        $data = $request->validate([
            'channelId' => 'required|string',
        ]);
        $id = $request->input('channelId');
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            $matched = $source->channel()->matchUrl($id);
            if ($matched) {
                ProcessChannelImport::dispatch(
                    $source->getSourceType(),
                    $matched,
                    $request->boolean('videos'),
                    $request->boolean('playlists')
                );
                continue;
            }
        }

        $message = 'Channel import started.';
        if (config('queue.default') == 'sync') {
            $message = 'Channel imported.';
        }
        return redirect('/admin')->with('message', $message);
    }

    public function missing()
    {
        $videos = Video::with(['channel', 'playlists'])
            ->doesntHave('files')
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
