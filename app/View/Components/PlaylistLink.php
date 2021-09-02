<?php

namespace App\View\Components;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\View\Component;

class PlaylistLink extends Component
{
    public ?Video $firstVideo;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public Playlist $playlist)
    {
        if ($playlist->firstItem !== null) {
            $this->firstVideo = $playlist->firstItem->video;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.playlist-link');
    }
}
