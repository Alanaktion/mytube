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

    public function __construct(
        public string $videoId,
        public ?string $downloadDir = null,
    ) {
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return object[]
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping($this->videoId))->dontRelease(),
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $video = Video::where('uuid', $this->videoId)->first();
        try {
            // Download the video
            $video->downloadVideo($this->downloadDir);

            // Delete any previous import errors on success
            ImportError::where('uuid', $this->videoId)
                ->where('type', $video->source_type)
                ->delete();
        } catch (Exception $e) {
            ImportError::updateOrCreate([
                'uuid' => $this->videoId,
                'type' => $video->source_type ?? '',
            ], [
                'reason' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
