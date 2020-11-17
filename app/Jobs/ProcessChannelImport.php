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

    protected $channelId;
    protected $importVideos;
    protected $importPlaylists;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $channelId, bool $importVideos = false, bool $importPlaylists = false)
    {
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
        Channel::importYouTube($this->channelId, $this->importVideos, $this->importPlaylists);
    }
}
