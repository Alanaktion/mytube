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
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected string $type,
        protected string $channelId,
        protected bool $importVideos = false,
        protected bool $importPlaylists = false,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
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
