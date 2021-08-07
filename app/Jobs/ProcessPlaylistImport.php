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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected string $type, protected string $playlistId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $playlist = Playlist::import($this->type, $this->playlistId);
    }
}
