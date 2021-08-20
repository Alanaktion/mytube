<?php

namespace App\View\Components;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\View\Component;

class VideoLink extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public Video $video,
        public bool $showChannel = false,
        public ?Playlist $playlist = null,
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.video-link');
    }
}
