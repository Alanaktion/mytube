<?php

namespace App\Jobs;

use App\Models\ImportError;
use App\Models\Video;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class DownloadVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

    public $videoId;
    public $downloadDir;

    public function __construct(string $videoId, ?string $downloadDir = null)
    {
        $this->videoId = $videoId;
        $this->downloadDir = $downloadDir;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [
            (new WithoutOverlapping($this->videoId))->dontRelease(),
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Import and download the video
            $video = Video::importYouTube($this->videoId);
            $video->downloadVideo($this->downloadDir);

            // Delete any previous import errors on success
            ImportError::where('uuid', $this->videoId)
                ->where('type', 'youtube')
                ->delete();
        } catch (Exception $e) {
            ImportError::updateOrCreate([
                'uuid' => $this->videoId,
                'type' => 'youtube',
            ], [
                'reason' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
