<?php

namespace App\View\Components;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\View\Component;

class VideoPlaylistItem extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public Video $video,
        public Playlist $playlist,
        public bool $current,
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.video-playlist-item');
    }
}
