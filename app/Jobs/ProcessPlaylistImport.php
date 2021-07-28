<?php

namespace App\Jobs;

use App\Models\Playlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPlaylistImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $playlistId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $type, string $playlistId)
    {
        $this->type = $type;
        $this->playlistId = $playlistId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Playlist::import($this->type, $this->playlistId);
    }
}
