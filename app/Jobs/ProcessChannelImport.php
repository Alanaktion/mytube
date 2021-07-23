<?php

namespace App\Jobs;

use App\Models\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessChannelImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $channelId;
    protected $importVideos;
    protected $importPlaylists;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $type, string $channelId, bool $importVideos = false, bool $importPlaylists = false)
    {
        $this->type = $type;
        $this->channelId = $channelId;
        $this->importVideos = $importVideos;
        $this->importPlaylists = $importPlaylists;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $channel = Channel::import($this->type, $this->channelId);

        // TODO: Update this once all sources can have videos/playlists imported
        if ($this->type == 'youtube') {
            if ($this->importVideos) {
                $channel->importVideos();
            }
            if ($this->importPlaylists) {
                $channel->importPlaylists();
            }
        }
    }
}
